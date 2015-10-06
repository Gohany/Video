<?php

class client
{

        public $context;
        public $client;
        public $command;
        public $identity;
        public $poll;
        public $session;
        
        const ADDRESS_PREFIX = 'cl';
        
        public static function cmdNode($node)
        {
                
                // lookup ip of node
                // return new client object with ip of as construct argument
                // do stuff? ez.
                
                $system = node::fromSystemNumber($node);
                print "STARTING CLIENT WITH " . $system->ip . PHP_EOL;
                return new client($system->ip);
        }

        public function __construct($ip = 'localhost', $server = 'controller')
        {
                $this->context = new ZMQContext();
                $this->client = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);

                //  Generate printable identity for the client
                $this->identity = self::ADDRESS_PREFIX . getmypid();
                $this->client->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->identity);
                
                if ($server == 'controller')
                {
                        $this->client->connect(zmqPorts::CLIENT_CONTROLLER_PROTOCOL . "://" . $ip . ":" . zmqPorts::CLIENT_CONTROLLER_INSTRUCTION);
                }
                elseif ($server == 'system')
                {
                        $this->client->connect(zmqPorts::CLIENT_SYSTEM_PROTOCOL . "://" . $ip . ":" . zmqPorts::CLIENT_SYSTEM_INSTRUCTION);
                        print "STARTING SYSTEM.. " . PHP_EOL;
                }
                
                $this->poll = new ZMQPoll();
                $this->poll->add($this->client, ZMQ::POLL_IN);
                
                if (!empty($_GET['sid']))
                {
                        $this->session = new session($_GET['sid']);
                        $this->command = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                        $this->command->bind(zmqPorts::CLIENT_VLISTEN_PROTOCOL . "://*:" . zmqPorts::CLIENT_VLISTEN_INSTRUCTION);
                }
                
        }
        
        public function command($who, $what)
        {
                $this->command = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                $this->command->bind(zmqPorts::CLIENT_VLISTEN_PROTOCOL . "://*:" . zmqPorts::CLIENT_VLISTEN_INSTRUCTION);
//                while (true)
//                {
                        sleep(2);
                        $this->command->send($who . $what);
//                }
        }

        public function request($cmd, $data)
        {
                
//                $zmsg = new Zmsg($this->client);
//                $zmsg->recv();
//                var_dump($zmsg);
                
                if (!defined('requestCmd::' . $cmd))
                {
                     throw new Exception("Undefined command");
                }
                
//                $data = [
//                    'id' => $id,
//                    'targets' => 'ALL',
//                    'height' => 0,
//                    'width' => 0,
//                    'x' => 0,
//                    'y' => 0,
//                ];
                
                $cmd = cmd::create($this->identity, constant('requestCmd::' . $cmd), $data);
                $cmd->send($this->client);
                var_dump($cmd);
                $read = $write = array();
                while (true)
                {
                        print "whiling.. " . PHP_EOL;
                        $events = $this->poll->poll($read, $write, 1000);
                        $zmsg = new Zmsg($this->client);
                        if ($events)
                        {
                                $zmsg->recv();
                                var_dump($zmsg);
                                //printf("%s: %s%s", $this->identity, $zmsg->body(), PHP_EOL);
                                break;
                        }
                }
        }

}