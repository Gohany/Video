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
        public $requests;

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
                                        print "REQUEST: " . $request . PHP_EOL;
                                        // do mysql stuff
                                        
                                        
                                        
                                        $port = $this->startingPort;

                                        print "PORT: " . $port . PHP_EOL;
                                        $zmsg->push($request);
                                        $zmsg->push($port);
                                        $zmsg->wrap(1234);
                                        //$zmsg->body_set($port);
                                        //$zmsg->push($request);
                                        $zmsg->set_socket($this->backend)->send();
                                }
                                elseif ($socket === $this->backend)
                                {
                                        // if good.. update mysql
                                        // start container logic
                                        // stuff
                                        $address = $zmsg->unwrap();
                                        print "ADDRESS: ".$address . PHP_EOL;
                                        $reply = $zmsg->body();
                                        print "REPLY: " . $reply . PHP_EOL;
                                        // update mysql
                                        if ($pid = $this->startFFMPEG('/var/www/h264.mp4', 1, $this->startingPort))
                                        {
                                                print "PORT: " . $this->startingPort . PHP_EOL;
                                                $this->startingPort++;

                                                $zmsg->body_set('success');
                                                $zmsg->set_socket($this->frontend)->send();
                                        }
                                        else
                                        {
                                                $zmsg->body_set('failure');
                                                $zmsg->set_socket($this->frontend)->send();
                                        }
                                }
                        }
                }
        }

        public function startFFMPEG($input, $id, $port)
        {
                print PHP_EOL . PHP_BINDIR . '/php /var/www/ffmpeg.php -i ' . $input . ' -id ' . $id . ' -p ' . $port . PHP_EOL;
                $process = new Process(PHP_BINDIR . '/php /var/www/ffmpeg.php -i ' . $input . ' -id ' . $id . ' -p ' . $port);
                print "PID: ".$process->pid . PHP_EOL;
                if ($process->status())
                {
                        return $process->pid;
                }
                return false;
        }

}

class Process
{

        public $pid;
        public $command;

        public function __construct($cl = false)
        {
                if ($cl != false)
                {
                        $this->command = $cl;
                        $this->runCom();
                }
        }

        private function runCom()
        {
                $command = 'nohup ' . $this->command . ' > /dev/null 2>&1 & echo $!';
                exec($command, $op);
                $this->pid = (int) $op[0];
        }

        public function setPid($pid)
        {
                $this->pid = $pid;
        }

        public function getPid()
        {
                return $this->pid;
        }

        public function status()
        {
                $command = 'ps -p ' . $this->pid;
                exec($command, $op);
                if (!isset($op[1]))
                {
                        return false;
                }
                else
                {
                        return true;
                }
        }

        public function start()
        {
                if ($this->command != '')
                {
                        $this->runCom();
                }
                else
                {
                        return true;
                }
        }

        public function stop()
        {
                $command = 'kill ' . $this->pid;
                exec($command);
                if ($this->status() == false)
                {
                        return true;
                }
                else
                {
                        return false;
                }
        }

}
