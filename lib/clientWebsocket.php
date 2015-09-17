<?php

class clientWS extends WebSocketServer
{

        //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
        public $instructionService;
        public $poll;
        public $zmsg;
        public $context;
        public $times;
        public $syncService;
        public $controllerSocket;
        public $clientRequests;
        
        const TIMEOUT = '100';
        const WHO_ALL = 'all';
        const WHO_ID = 'id';
        
        const ADDRESS_PREFIX = 'ws';

        public function __construct($addr, $port, $bufferLength = 2048)
        {
                
                $this->context = new ZMQContext();
                // direct client instruction
                $this->identity = self::ADDRESS_PREFIX . getmypid();
                $this->instructionService = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->instructionService->connect(zmqPorts::CLIENT_WEBSOCKET_PROTOCOL . "://" . zmqPorts::CLIENT_WEBSOCKET_INSTRUCTION);
                
                $this->syncService = $this->context->getSocket(ZMQ::SOCKET_REP);
                $this->syncService->connect("tcp://localhost:" . zmqPorts::VSYNC_WEBSOCKET_INSTRUCTION);
                
                $this->controllerSocket = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                //$this->controllerSocket->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->identity);
                $this->controllerSocket->connect(zmqPorts::CONTROLLER_WEBSOCKET_PROTOCOL . "://" . zmqPorts::CONTROLLER_WEBSOCKET_INSTRUCTION);
                //$hello = new websocketAction($this->zmsg, $this);
                
                $this->poll = new ZMQPoll();
                $this->poll->add($this->instructionService, ZMQ::POLL_IN);
                $this->poll->add($this->controllerSocket, ZMQ::POLL_IN);
                $this->poll->add($this->syncService, ZMQ::POLL_IN | ZMQ::POLL_OUT);

                parent::__construct($addr, $port, $bufferLength);
                
        }

        protected function tick()
        {
                $readable = $writeable = array();
                $events = $this->poll->poll($readable, $writeable, self::TIMEOUT);
                if ($events > 0)
                {
                        foreach ($readable as $socket)
                        {
                                $zmsg = new Zmsg($socket);
                                $zmsg->recv();
                                if ($socket === $this->instructionService)
                                {
                                        print "NEW CLIENT REQUEST" . PHP_EOL;
                                       
                                        $address = $zmsg->unwrap();
                                        $addr = $zmsg->pop();
                                        $who = $zmsg->pop();
                                        $cmd = $zmsg->pop();
                                        print "CMD: ".$cmd . PHP_EOL;
                                        print "WHO: ".$who . PHP_EOL;
                                        print "ADDR: ".$addr . PHP_EOL;
                                        // do stuff
                                        $this->doCommand($cmd, $who);
                                        $zmsg->body_set('success')->wrap($address);
                                        $zmsg->send(true);
                                }
                                elseif ($socket === $this->controllerSocket)
                                {
                                        print "controller socket: " . PHP_EOL;
                                        $this->clientRequests[$zmsg->address()] = new request($zmsg, $this);
                                }
                        }
                }
        }
        
        public function updateClients($targets, $cmd, $id)
        {
                print "TARGETS: " . $targets . PHP_EOL;
                print "CMD: " . $cmd . PHP_EOL;
                print "ID: " . $id . PHP_EOL;
                foreach ($this->users as $user)
                {
                        $message = $cmd . "|http://192.168.2.6/vListen.php?id=" . $id;
                        $this->send($user, $message);
                }
        }
        
        public function doCommand($cmd, $who)
        {
                print "DOING COMMAND" . PHP_EOL;
                
                switch ($cmd)
                {
                        case "":
                                break;
                }
                
                foreach ($this->users as $user)
                {
                        $message = $cmd . '&rand=' . sprintf("%04X", rand(0, 0x10000));
                        $this->send($user, $message);
                }
        }
        
        public function play($who = self::WHO_ALL)
        {
                switch ($who)
                {
                        
                }
        }

        protected function process($user, $message)
        {
                
                $explode = explode('|', $message);
                $video = $explode[0];
                $time = $explode[1];
                
                $this->times[$video][$user->id] = $time;
                
                var_dump($this->times);
                $this->send($user, 'ack');
        }

        protected function connected($user)
        {
                $this->send($user, "sync|" . min($this->times[5]));
        }

        protected function closed($user)
        {
                print "PROCESSING CLOSED FOR ".$user->id . PHP_EOL;
                foreach ($this->times as $video => $array)
                {
                        if (isset($array[$user->id]))
                        {
                                unset($this->times[$video][$user->id]);
                        }
                }
        }

}