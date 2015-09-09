<?php

require_once 'includes.php';

$controller = new vController;
$controller->run();

class vController
{

        public $context;
        public $sockets;
        public $poll;
        public $ports;
        public $workers;
        public $requests;
        public $feeds;

        const STARTING_PORT = 6400;
        const CONCURRENT_PORTS = 100;

        public function __construct()
        {

                $this->context = new ZMQContext();
                $this->sockets['client'] = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->sockets['vSync'] = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                $this->sockets['websocket'] = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);

                $this->sockets['client']->bind(zmqPorts::CLIENT_CONTROLLER_PROTOCOL . "://*:" . zmqPorts::CLIENT_CONTROLLER_INSTRUCTION);
                $this->sockets['vSync']->bind(zmqPorts::CONTROLLER_VSYNC_PROTOCOL . "://" . zmqPorts::CONTROLLER_VSYNC_INSTRUCTION);
                $this->sockets['websocket']->bind(zmqPorts::CONTROLLER_WEBSOCKET_PROTOCOL . "://" . zmqPorts::CONTROLLER_WEBSOCKET_INSTRUCTION);

                //  Initialize poll set
                $this->poll = new ZMQPoll();
                $this->poll->add($this->sockets['client'], ZMQ::POLL_IN);
                $this->poll->add($this->sockets['vSync'], ZMQ::POLL_IN);
                $this->poll->add($this->sockets['websocket'], ZMQ::POLL_IN | ZMQ::POLL_OUT);

                $this->ports = array_fill(self::STARTING_PORT, self::CONCURRENT_PORTS, 1);
        }

        public function run()
        {
                $read = $write = array();
                while (true)
                {
                        $this->poll->poll($read, $write);
                        foreach ($read as $socket)
                        {
                                $zmsg = new Zmsg($socket);
                                $zmsg->recv();
                                if ($socket === $this->sockets['client'])
                                {
                                        $clientRequest = new clientRequest($zmsg, $this);
                                }
                                elseif ($socket === $this->sockets['vSync'])
                                {
                                        // if good.. update mysql
                                        // start container logic
                                        // stuff
                                        var_dump($zmsg);
                                        $address = $zmsg->unwrap();
                                        print "ADDRESS: " . $address . PHP_EOL;
                                        list($cmd, $id, $reply) = $zmsg->extract();
                                        print "ID: " . $id . PHP_EOL;
                                        print "REPLY: " . $reply . PHP_EOL;
                                        // update mysql
                                        if (!empty($this->feeds[$id]) && ($pid = $this->startFFMPEG($this->feeds[$id]->input, $id, $this->feeds[$id]->port)))
                                        {

                                                $this->feeds[$id]->startFeed($pid);
                                                print "PORT: " . $this->feeds[$id]->port . PHP_EOL;
                                                
                                                $zmsg->body_set('success');
                                                $zmsg->set_socket($this->sockets['client'])->wrap('lulz')->send(false);
                                                var_dump($zmsg);
                                        }
                                        else
                                        {
                                                $this->ports[$this->feeds[$id]->port] = 1;
                                                $zmsg->body_set('failure');
                                                $zmsg->set_socket($this->sockets['client'])->send();
                                        }
                                }
                        }
                }
        }

        public function startFFMPEG($input, $id, $port)
        {
                print PHP_EOL . PHP_BINDIR . '/php /var/www/ffmpeg.php -i ' . $input . ' -id ' . $id . ' -p ' . $port . PHP_EOL;
                $process = new Process(PHP_BINDIR . '/php /var/www/ffmpeg.php -i ' . $input . ' -id ' . $id . ' -p ' . $port);
                print "PID: " . $process->pid . PHP_EOL;
                if ($process->status())
                {
                        return $process->pid;
                }
                return false;
        }

}
