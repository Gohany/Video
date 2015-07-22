<?php

require_once 'zmsg.php';

$controller = new controller;
$controller->run();

class controller
{

        // listen for REQs
        // queue for logic
        // peform logic
        //      register everything with MySQL
        //      req - rep to proxy, tell it new port
        //      fork / start new container logic proc
        //      update MySQL

        const ZMQ_CLIENT_ROUTER_PORT = '6200';
        const ZMQ_PROXY_DEALER_PORT = 'backend';

        public $context;
        public $frontend;
        public $backend;
        public $poll;
        public $startingPort = 6400;
        public $workers;

        public function __construct()
        {
                
                $this->context = new ZMQContext();
                $this->frontend = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->backend = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                $this->frontend->bind("tcp://*:" . self::ZMQ_CLIENT_ROUTER_PORT);
                $this->backend->bind("ipc://" . self::ZMQ_PROXY_DEALER_PORT);

                //  Initialize poll set
                $this->poll = new ZMQPoll();
                $this->poll->add($this->frontend, ZMQ::POLL_IN);
                $this->poll->add($this->backend, ZMQ::POLL_IN);

        }

        public function run()
        {
                $read = $write = array();
                //  Switch messages between frontend and backend
                while (true)
                {
                        $this->poll->poll($read, $write);
                        foreach ($read as $socket)
                        {
                                $zmsg = new Zmsg($socket);
                                $zmsg->recv();
                                if ($socket === $this->frontend)
                                {
                                        $request = $zmsg->body();
                                        print "REQUEST: ".$request . PHP_EOL;
                                        // do mysql stuff
                                        
                                        $port = $this->startingPort . PHP_EOL;
                                        
                                        print "PORT: ".$port;
                                        $zmsg->body_set($port);
                                        $zmsg->set_socket($this->backend)->send();
                                }
                                elseif ($socket === $this->backend)
                                {
                                        // if good.. update mysql
                                        // start container logic
                                        // stuff
                                        $reply = $zmsg->body();
                                        print "REPLY: ".$reply . PHP_EOL;
                                        // update mysql
                                        $this->startFFMPEG(1, $this->startingPort);
                                        print "PORT: ".$this->startingPort . PHP_EOL;
                                        $this->startingPort++;
                                        
                                        
                                        $zmsg->body_set('success');
                                        $zmsg->set_socket($this->frontend)->send();
                                }
                        }
                }
        }
        
        public function startFFMPEG2()
        {
                exec(PHP_BINDIR . '/php /var/www/ffmpeg.php > /dev/null 2>&1 &');       
        }
        
        public function startFFMPEG($id, $port)
        {
                $this->workers[$port] = popen(PHP_BINDIR . '/php /var/www/ffmpeg.php ' . $id . ' ' . $port);
        }

}
