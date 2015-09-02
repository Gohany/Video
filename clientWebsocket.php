#!/usr/bin/env php
<?php

// INTERNAL NETWORK.... LIKE BEFORE
// fuckin EVERY TV THAT HAS THE POSSIBILITY OF PLAYING THE VIDEO NEEDS TO START LISTENING BEFORE THE BROADCAST. DO THIS DYNAMICALLY.
// they will all catch the broadcast.

require_once('websockets.php');
require_once('includes.php');

class clientWS extends WebSocketServer
{

        //protected $maxBufferSize = 1048576; //1MB... overkill for an echo server, but potentially plausible for other applications.
        public $instructionService;
        public $poll;
        public $zmsg;
        public $context;
        public $times;
        public $syncService;

        const ZMQ_INSTRUCTION_PORT = 'clientInstruction';
        const TIMEOUT = '100';
        const ZMQ_SYNC_PORT = 5557;
        const WHO_ALL = 'all';
        const WHO_ID = 'id';

        public function __construct($addr, $port, $bufferLength = 2048)
        {
                
                $this->context = new ZMQContext();
                $this->instructionService = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->instructionService->connect("ipc://" . self::ZMQ_INSTRUCTION_PORT);

                $this->zmsg = new Zmsg($this->instructionService);
                
                $this->syncService = $this->context->getSocket(ZMQ::SOCKET_REQ);
                $this->syncService->connect("tcp://localhost:" . self::ZMQ_SYNC_PORT);
                
                $this->poll = new ZMQPoll();
                $this->poll->add($this->instructionService, ZMQ::POLL_IN);
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

$echo = new clientWS("0.0.0.0", "9000");
try
{
        $echo->run();
}
catch (Exception $e)
{
        $echo->stdout($e->getMessage());
}