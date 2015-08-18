#!/usr/bin/env php
<?php
require_once('websockets.php');
require_once('includes.php');

class clientWS extends WebSocketServer
{

        //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
        public $instructionService;
        public $poll;
        public $zmsg;
        public $context;

        const ZMQ_INSTRUCTION_PORT = 'clientInstruction';
        const TIMEOUT = '100';

        public function __construct($addr, $port, $bufferLength = 2048)
        {
                
                $this->context = new ZMQContext();
                $this->instructionService = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->instructionService->connect("ipc://" . self::ZMQ_INSTRUCTION_PORT);

                $this->poll = new ZMQPoll();
                $this->poll->add($this->instructionService, ZMQ::POLL_IN);

                $this->zmsg = new Zmsg($this->instructionService);

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
                                if ($socket === $this->instructionService)
                                {
                                        print "NEW CLIENT REQUEST" . PHP_EOL;
                                        $this->zmsg->recv();
                                        $address = $this->zmsg->unwrap();
                                        $addr = $this->zmsg->pop();
                                        $who = $this->zmsg->pop();
                                        $cmd = $this->zmsg->pop();
                                        print "CMD: ".$cmd . PHP_EOL;
                                        print "WHO: ".$who . PHP_EOL;
                                        print "ADDR: ".$addr . PHP_EOL;
                                        // do stuff
                                        $this->doCommand($cmd, $who);
                                        $this->zmsg->body_set('success')->wrap($address);
                                        $this->zmsg->send(true);
                                }
                        }
                }
        }
        
        public function doCommand($cmd, $who)
        {
                print "DOING COMMAND" . PHP_EOL;
                foreach ($this->users as $user)
                {
                        $message = $cmd . '&rand=' . sprintf("%04X", rand(0, 0x10000));
                        $this->send($user, $message);
                }
        }

        protected function process($user, $message)
        {
                $this->send($user, $message);
        }

        protected function connected($user)
        {
                
        }

        protected function closed($user)
        {
                
        }

}

$echo = new clientWS("0.0.0.0", "9000");
try
{
        $echo->run();
}
catch (Exception $e)
{
        $echo->stdout($e->getMessage());
}