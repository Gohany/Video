<?php

require_once 'includes.php';

class clientAction extends action
{

        const ADDRESS_PREFIX = 'cr';

        public $id;
        public static $additionals = [
                'id',
        ];

        public function __construct(Zmsg $zmsg, vController $controller)
        {

                parent::__construct($zmsg);

                switch ($this->cmd)
                {
                        // add another source to current viewing
                        case clientCmd::ADD_SOURCE:
                        // make new source the only source
                        case clientCmd::NEW_SINGLE_SOURCE:
                                $this->startPublisher($controller);
                                break;
                        // remove a viewed source
                        case clientCmd::REMOVE_SOURCE:

                                break;
                        // make current source the only source
                        case clientCmd::SINGLE_SOURCE:

                                break;
                        // make blank
                        case clientCmd::REMOVE_ALL:

                                break;
                }
        }

        public function startPublisher(vController $controller)
        {

                if (!is_numeric($this->id) || !($controller->feeds[$this->id] = feed::fromID($this->id)))
                {
                        $this->zmsg->body_set('failure')->send();
                        return false;
                }

                $port = array_search(1, $controller->ports);
                $controller->ports[$port] = 0;
                $controller->feeds[$this->id]->port = $port;
                $controller->feeds[$this->id]->update();

                $this->zmsg->set(self::address(), vSyncCmd::PUBLISH, $port);
                $this->zmsg->set_socket($controller->sockets['vSync'])->send();
        }

        public function reply(vController $controller, $message)
        {
                print "replying.. " . PHP_EOL;
                $this->zmsg->body_set($message)->wrap($this->address)->set_socket($controller->sockets['client'])->send();
        }

}

class controllerAction extends action
{

        public $port;

        const ADDRESS_PREFIX = 'cr';

        public static $additionals = [
                'port',
        ];

        public function __construct(Zmsg $zmsg, zmqSync $vSync)
        {
                parent::__construct($zmsg);
                var_dump($this);
                switch($this->cmd)
                {
                        case vSyncCmd::REMOVE_PORT:
                                $this->unsubscribe($vSync);
                                break;
                        case vSyncCmd::PUBLISH:
                                $this->subscribe($vSync);
                                break;
                }
        }
        
        public function unsubscribe(zmqSync $vSync)
        {
                // stuff
                print "HERE!" . PHP_EOL;
                $success = $vSync->disconnectBackend($this->port) ? 'success' : 'failure';
                $this->zmsg->push(controlCmd::REMOVE_PORT);
                $this->zmsg->body_set($success)->wrap($vSync->identity);
                $this->zmsg->send();
        }

        public function subscribe(zmqSync $vSync)
        {
                $previousObjects = $this->extractPreviousObjects('clientAction', 'controllerAction');
                $vSync->registerBackend($previousObjects['controllerAction']->port);
                $this->zmsg->push(controlCmd::START_FFMPEG);
                $this->zmsg->body_set('success')->wrap($vSync->identity);
                $this->zmsg->send();
        }

}

class vSyncAction extends action
{

        const ADDRESS_PREFIX = 'vs';

        public $response;
        public $controllerAddress;
        public $port;
        public $clientAddress;
        public $cmd;
        public $reply;
        public static $additionals = [
                'port',
        ];

        public function __construct(Zmsg $zmsg, vController $controller)
        {
                parent::__construct($zmsg);
                var_dump($this);
                switch ($this->cmd)
                {
                        case controlCmd::START_FFMPEG:
                                $this->publishSuccess($controller);
                                break;
                        case controlCmd::REMOVE_PORT:
                                $this->removePort($controller);
                                break;
                }
        }

        public function publishSuccess(vController $controller)
        {

                $previousObjects = $this->extractPreviousObjects('clientAction', 'controllerAction');
                $id = $previousObjects['clientAction']->id;
                $address = $previousObjects['clientAction']->address;
                // update mysql
                if (!empty($controller->feeds[$id]) && ($pid = $controller->startFFMPEG($controller->feeds[$id]->input, $id, $controller->feeds[$id]->port)))
                {
                        $controller->feeds[$id]->startFeed($pid);
                        $controller->clientRequests[$address]->reply($controller, 'success');
                }
                else
                {
                        $controller->ports[$controller->feeds[$id]->port] = 1;
                        $controller->clientRequests[$address]->reply($controller, 'failure');
                }
        }
        
        public function removePort(vController $controller)
        {
                $previousObjects = $this->extractPreviousObjects('controllerAction');
                var_dump($previousObjects);
                $controller->ports[$previousObjects['controllerAction']->port] = 1;
                unset($controller->ffmpegs[$previousObjects['controllerAction']->port]);
        }

}

class action
{

        public $zmsg;
        public $cmd;
        public $feed;
        public $address;
        public $message;
        public static $defaults = [
                'address',
                'cmd',
        ];

        public static function address()
        {
                return static::ADDRESS_PREFIX . getmypid();
        }

        public function extractPreviousObjects()
        {
                $objects = array();
                $offset = 1;
                foreach (func_get_args() as $argument)
                {
                        $objects[$argument] = new stdClass;
                        $extract = !empty($argument::$additionals) ? array_reverse(array_merge(self::$defaults, $argument::$additionals)) : array_reverse(self::$defaults);
                        foreach (array_slice(array_reverse($this->zmsg->extract()), $offset, count($extract)) as $key => $value)
                        {
                                if (isset($extract[$key]) && property_exists($argument, $extract[$key]))
                                {
                                        $objects[$argument]->{$extract[$key]} = $value;
                                }
                        }
                        $offset += count($extract);
                }

                return $objects;
        }

        public function __construct(Zmsg $zmsg)
        {
                $this->zmsg = $zmsg;
                $this->message = $zmsg->body();
                $extract = !empty(static::$additionals) ? array_merge(self::$defaults, static::$additionals) : self::$defaults;
                foreach ($zmsg->extract() as $key => $value)
                {
                        if (isset($extract[$key]) && property_exists($this, $extract[$key]))
                        {
                                $this->{$extract[$key]} = $value;
                        }
                }
        }

}