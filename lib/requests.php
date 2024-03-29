<?php

require_once 'includes.php';

class cmd
{
        
        // address
        // cmd?
        // properties - json?
        public $address;
        public $cmd;
        public $data;
        public $history = [];
        
        public function send($socket)
        {
                $zmsg = new Zmsg($socket);
                if (!empty($this->history))
                {
                        foreach (array_reverse($this->history) as $history)
                        {
                                $zmsg->set($history['address'], $history['cmd'], json_encode($history['data']));
                        }
                }
                $sockOpt = $socket->getSockOpt(ZMQ::SOCKOPT_IDENTITY);
                if (empty($sockOpt))
                {
                        $zmsg->set($this->address, $this->cmd, json_encode($this->data));
                }
                else
                {
                        $zmsg->set($this->cmd, json_encode($this->data));
                }
                
                $zmsg->send();
        }
        
        public static function firstCMD($object)
        {
                $history = end($object->history);
                $address = $history['address'];
                $cmd = $history['cmd'];
                $data = (array) $history['data'];
                return self::create($address, $cmd, $data);
        }
        
        public static function fromType($object, $type)
        {
                foreach ($object->history as $history)
                {
                        if ($history['type'] == $type)
                        {
                                return self::create($history['address'], $history['cmd'], (array) $history['data']);
                        }
                }
                return false;
        }
        
        public static function fromCMDHistory($object, $cmd)
        {
                foreach ($object->history as $history)
                {
                        if ($history['cmd'] == $cmd)
                        {
                                return self::create($history['address'], $cmd, (array) $history['data']);
                        }
                }
                return false;
        }
        
        public static function create($address, $cmd, $data = array())
        {
                $zmsg = new Zmsg;
                $zmsg->set($address, $cmd, json_encode($data));
                return new cmd($zmsg);
        }
        
        public function push($address, $cmd, $data = array())
        {
                array_unshift($this->history, ['address' => $this->address, 'cmd' => $this->cmd, 'data' => $this->data]);
                $this->address = $address;
                $this->type = $this->type($address);
                $this->cmd = $cmd;
                $this->data = $data;
        }
        
        public function __construct(Zmsg $zmsg = null)
        {
                if (!empty($zmsg))
                {
                        if (($zmsg->parts() % 3) == 0)
                        {
                                foreach (range(0, ($zmsg->parts() - 3), 3) as $m)
                                {
                                        for ($i=1*$m; $i<3+$m; $i++)
                                                //1 * 0, 0<3
                                                //1 * 3, 3<6
                                                //1 * 6, 6<9
                                        {
                                                if (empty($this->address) || empty($this->cmd) || !isset($this->data))
                                                {
                                                        switch ($i)
                                                        {
                                                                case 0:
                                                                        $this->address = $zmsg->pop();
                                                                        $this->type = $this->type($this->address);
                                                                        break;
                                                                case 1:
                                                                        $this->cmd = $zmsg->pop();
                                                                        break;
                                                                case 2:
                                                                        $this->data = json_decode($zmsg->pop());
                                                        }
                                                }
                                                else
                                                {
                                                        switch ($i)
                                                        {
                                                                case 0 + $m:
                                                                        $this->history[$m]['address'] = $zmsg->pop();
                                                                        $this->history[$m]['type'] = $this->type($this->history[$m]['address']);
                                                                        break;
                                                                case 1 + $m:
                                                                        $this->history[$m]['cmd'] = $zmsg->pop();
                                                                        break;
                                                                case 2 + $m:
                                                                        $this->history[$m]['data'] = json_decode($zmsg->pop());
                                                        }
                                                }
                                        }
                                }
                        }
                        else
                        {
                                print "PARTS NOT RIGHT!" . PHP_EOL;
                        }
                }
        }
        
        public function type($type)
        {
                switch (substr($type, 0, 2))
                {
                        case client::ADDRESS_PREFIX:
                                return 'client';
                                break;
                        case vController::ADDRESS_PREFIX:
                                return 'vController';
                                break;
                        case vSync::ADDRESS_PREFIX:
                                return 'vSync';
                                break;
                        case clientWS::ADDRESS_PREFIX:
                                return 'clientWS';
                                break;
                        case system::ADDRESS_PREFIX:
                                return 'system';
                                break;
                }
        }
        
}


class request
{
        /* @var $zmsg Zmsg */
        public $zmsg;
        /* @var $cmd cmd */
        public $cmd;
        
        public function __construct(Zmsg $zmsg, $object)
        {
                
                $this->zmsg = $zmsg;
                print "RECEIVING: " . PHP_EOL;
                var_dump($zmsg);
                
                $this->cmd = new cmd($zmsg);
                error_log('NEW REQUEST: ' . var_export($this->cmd, true) . PHP_EOL, 3, '/var/www/requests.log');
                switch ($this->cmd->cmd)
                {
                        // ADD SOURCE
                        // 
                        case requestCmd::ADD_SOURCE:
                                $this->wsAddSource($object);
                                break;
                        case requestCmd::NEW_SINGLE_SOURCE:
                                $this->startFrontend($object);
                                break;
                        case requestCmd::REMOVE_SOURCE:
                                $this->removeFrontend($object);
                                break;
                        case requestCmd::SINGLE_SOURCE:
                                
                                break;
                        case requestCmd::REMOVE_ALL:
                                
                                break;
                        case requestCmd::PUBLISH:
                                
                                break;
                        case requestCmd::REMOVE_PORT:
                                if ($object instanceof vController)
                                {
                                        $this->reactivatePort($object);
                                }
                                elseif ($object instanceof vSync)
                                {
                                        $this->unsubscribe($object);
                                }
                                break;
                        case requestCmd::START_ENCODE:
                                $this->encode($object);
                                break;
                        case requestCmd::SUBSCRIBE:
                                if ($object instanceof vController)
                                {
                                        $this->forwardSubscribe($object);
                                }
                                elseif ($object instanceof vSync)
                                {
                                        $this->subscribe($object);
                                }
                                break;
                        case requestCmd::START_DISPLAY:
                                $this->startVideo($object);
                                break;
                        case requestCmd::SUCCESS:
                                $this->replyClient($object);
                                break;
                        case requestCmd::STOP_DISPLAY:
                                $this->stopVideo($object);
                                break;
                        case requestCmd::STOP_ENCODE:
                                $this->stopEncoding($object);
                                break;
                        case requestCmd::START_NEW_DISPLAY:
                                $this->startVideo($object);
                                break;
                        case requestCmd::ADD_LAYER:
                        case requestCmd::REMOVE_LAYER:
                        case requestCmd::MOVE_LAYER:
                                if ($object instanceof vController)
                                {
                                        $this->sendWebsocketCommand($object);
                                }
                                elseif ($object instanceof clientWS)
                                {
                                        $this->runWebsocketCommand($object);
                                }
                                break;
                        case requestCmd::ASSIGN_SYSTEM_NUMBER:
                                $this->assignSystemNumber($object);
                                break;
                        case requestCmd::BROADCAST_ASSIGN_NUMBER:
                                $this->broadcastAssignNumber($object);
                                break;
                        case requestCmd::SYSTEM_STOP:
                                $this->stopSystem($object);
                                break;
                        case requestCmd::SYSTEM_RESTART:
                                $this->restartSystem($object);
                                break;
                        case requestCmd::SYSTEM_STOP_ALL:
                                $this->stopAllSystems($object);
                                break;
                        case requestCmd::SYSTEM_RESTART_ALL:
                                $this->restartAllSystems($object);
                }
                
        }
        
        public function restartSystem(system $system)
        {
                $system->shutdownProcs();
                $restart = new Process(PHP_BINDIR . '/php /var/www/start.php -f system.php');
                $this->cmd->push($this->cmd->address, requestCmd::SUCCESS);
                $this->cmd->send($system->client);
                exit;
        }
        
        public function restartAllSystems(system $system)
        {
                $clientIdentity = $this->cmd->address;
                $this->cmd->push($system->identity, requestCmd::SYSTEM_RESTART, $this->cmd->data);
                $this->cmd->send($system->publish);
                $system->shutdownProcs();
                $restart = new Process(PHP_BINDIR . '/php /var/www/start.php -f system.php');
                $this->cmd->push($clientIdentity, requestCmd::SUCCESS);
                $this->cmd->send($system->client);
                exit;
        }
        
        public function stopAllSystems(system $system)
        {
                $clientIdentity = $this->cmd->address;
                $this->cmd->push($system->identity, requestCmd::SYSTEM_STOP, $this->cmd->data);
                $this->cmd->send($system->publish);
                $system->shutdownProcs();
                $this->cmd->push($clientIdentity, requestCmd::SUCCESS);
                $this->cmd->send($system->client);
                exit;
        }
        
        public function stopSystem(system $system)
        {
                $system->shutdownProcs();
                $this->cmd->push($this->cmd->address, requestCmd::SUCCESS);
                $this->cmd->send($system->client);
                exit;
        }
        
        public function broadcastAssignNumber(system $system)
        {
                $clientIdentity = $this->cmd->address;
                $this->cmd->push($system->identity, requestCmd::ASSIGN_SYSTEM_NUMBER, $this->cmd->data);
                $this->cmd->send($system->publish);
                $this->cmd->push($clientIdentity, requestCmd::SUCCESS);
                $this->cmd->send($system->client);
        }
        
        public function assignSystemNumber(system $system)
        {
                if ($node = node::fromMacAddress($this->cmd->data->macAddress))
                {
                        $node->setSystemNumber($this->cmd->data->system_number, $this->cmd->data->macAddress);
                }
        }
        
        public function replyClient(vController $controller, $success = 'success')
        {
                if ($clientObject = cmd::fromType($this->cmd, 'client'))
                {
                        $this->cmd->push($clientObject->address, requestCmd::SUCCESS);
                        $this->cmd->send($controller->sockets['client']);
                }
        }
        
        public function sendWebsocketCommand(vController $controller)
        {
                $this->cmd->push($controller->identity, $this->cmd, $this->cmd->data);
                $this->cmd->send($controller->sockets['websocket']);
        }
        
        public function runWebsocketCommand(clientWS $websocket)
        {
                $websocket->runCommand($this->cmd->cmd, $this->cmd->data);
                $this->cmd->push($websocket->identity, requestCmd::SUCCESS);
                $this->cmd->send($websocket->controllerSocket);
        }
        
        
        public function stopEncoding(vController $controller)
        {
                $controller->stopFeed($this->cmd->data->id);
                if ($firstCmd = cmd::firstCMD($this->cmd))
                {
                        $firstCmd->push($firstCmd->address, requestCmd::SUBSCRIBE, ['status' => 'success']);
                        $firstCmd->send($controller->sockets['client']);
                }
                else
                {
                        print "couldn't get previous object";
                }
        }
        
        public function reactivatePort(vController $controller)
        {
                if ($this->cmd->data->status == 'success')
                {
                        $controller->reactivatePort($this->cmd->data->port);
                }
        }
        
        // end points
        public function encode(vController $controller)
        {
                print "ENCODE! " . PHP_EOL;
                if (
                        ($firstCmd = cmd::fromCMDHistory($this->cmd, requestCmd::ADD_SOURCE)) || 
                        ($firstCmd = cmd::fromCMDHistory($this->cmd, requestCmd::NEW_SINGLE_SOURCE)) || 
                        ($firstCmd = cmd::fromCMDHistory($this->cmd, requestCmd::SINGLE_SOURCE)))
                {
                        $id = $firstCmd->data->id;
                        if (!empty($controller->feeds[$id]) && ($pid = $controller->startFFMPEG($controller->feeds[$id]->input, $id, $controller->feeds[$id]->port)))
                        {
                                $controller->feeds[$id]->startFeed($pid);
                                $this->cmd->push($firstCmd->address, requestCmd::SUCCESS);
                                $this->cmd->send($controller->sockets['client']);
                        }
                        else
                        {
                                print "couldn't start ffmpeg.. " . PHP_EOL;
                        }
                }
                else
                {
                        print "COULD NOT LOAD CMD" . PHP_EOL;
                }
                
        }
        
        public function removeFrontend(vController $controller)
        {
                $this->cmd->push($controller->identity, requestCmd::STOP_DISPLAY, $this->cmd->data);
                $this->cmd->send($controller->sockets['websocket']);
        }
        
        public function wsAddSource(vController $controller)
        {
                $this->cmd->push($controller->identity, requestCmd::START_NEW_DISPLAY, $this->cmd->data);
                $this->cmd->send($controller->sockets['websocket']);
        }
        
        public function startFrontend(vController $controller)
        {
                $this->cmd->push($controller->identity, requestCmd::START_DISPLAY, $this->cmd->data);
                $this->cmd->send($controller->sockets['websocket']);
        }
        
        public function startVideo(clientWS $websocket)
        {
                $websocket->updateClients($this->cmd->data->targets, $this->cmd->cmd, $this->cmd->data->id);
                $this->cmd->push($websocket->identity, requestCmd::SUBSCRIBE);
                $this->cmd->send($websocket->controllerSocket);
        }
        
        public function stopVideo(clientWS $websocket)
        {
                $websocket->updateClients($this->cmd->data->targets, $this->cmd->cmd, $this->cmd->data->id);
                $this->cmd->push($websocket->identity, requestCmd::STOP_ENCODE, ['id' => $this->cmd->data->id]);
                $this->cmd->send($websocket->controllerSocket);
        }
        
        public function forwardSubscribe(vController $controller)
        {
                
                if (!($port = $controller->nextPort()))
                {
                        // make controller vs other exceptions
                        throw new Exception('No ports available');   
                }
                
                if ($firstCmd = cmd::firstCMD($this->cmd))
                {
                        $controller->startFeed($firstCmd->data->id, $port);
                        $this->cmd->push($controller->identity, requestCmd::SUBSCRIBE, ['port' => $port]);
                        $this->cmd->send($controller->sockets['vSync']);
                }
                else
                {
                        print "couldn't get previous object";
                }
                
        }
        
        public function unsubscribe(vSync $vSync)
        {
                $success = $vSync->disconnectBackend($this->cmd->data->port) ? 'success' : 'failure';
                $this->cmd->push($vSync->identity, requestCmd::REMOVE_PORT, ['status' => $success, 'port' => $this->cmd->data->port]);
                $this->cmd->send($vSync->instructionService);
        }
        

        public function subscribe(vSync $vSync)
        {
                var_dump($this->cmd);
                $vSync->registerBackend($this->cmd->data->port);
                $this->cmd->push($vSync->identity, requestCmd::START_ENCODE);
                $this->cmd->send($vSync->instructionService);
        }
        
}