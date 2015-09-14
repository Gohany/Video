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
        public $clientRequests;
        public $vSyncRequests;
        public $ffmpegs = array();
        public $portRequests;

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
                        $this->poll->poll($read, $write, 1000);
                        foreach ($read as $socket)
                        {
                                $zmsg = new Zmsg($socket);
                                $zmsg->recv();
                                if ($socket === $this->sockets['client'])
                                {
                                        $this->clientRequests[$zmsg->address()] = new clientAction($zmsg, $this);
                                }
                                elseif ($socket === $this->sockets['vSync'])
                                {
                                        $this->vSyncRequests[$zmsg->address()] = new vSyncAction($zmsg, $this);
                                        //$this->vSyncRequests[$zmsg->address()]->publishSuccess($this);
                                }
                        }
                        
                        foreach ($this->ffmpegs as $port => $process)
                        {
                                if (!$process->status())
                                {
                                        $zmsg = new Zmsg($this->sockets['vSync']);
                                        $zmsg->set(vSyncCmd::REMOVE_PORT, $port, 'cmd');
                                        $zmsg->wrap(controllerAction::address());
                                        $zmsg->send();
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
                        $this->ffmpegs[$port] = $process;
                        return $process->pid;
                }
                return false;
        }

}