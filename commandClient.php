<?php

require_once 'includes.php';

class commandClient
{
        
        public $cmd;
        public $who;
        public $context;
        public $wsServer;
        public $poll;

        public function __construct()
        {

                $this->context = new ZMQContext();
                $this->wsServer = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                $this->wsServer->bind(zmqPorts::CLIENT_WEBSOCKET_PROTOCOL . "://" . zmqPorts::CLIENT_WEBSOCKET_INSTRUCTION);

                //  Initialize poll set
                $this->poll = new ZMQPoll();
                $this->poll->add($this->wsServer, ZMQ::POLL_IN);
                
        }
        
        public function send($cmd, $who)
        {
                if (!empty($cmd) && !empty($who))
                {
                        print "SENDING .. " . PHP_EOL;
                        $zmsg = new Zmsg($this->wsServer);
                        $zmsg->push($cmd);
                        $zmsg->push($who);
                        $zmsg->wrap('cmdClient');
                        $zmsg->send();
                }
        }
        
}

$stdin =  stdin::input();
$commandClient = new commandClient;
$commandClient->send($stdin->cmd, $stdin->who);