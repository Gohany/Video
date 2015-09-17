<?php

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
        public $websocketRequests;
        public $ffmpegs = array();
        public $portRequests;

        const STARTING_PORT = 6400;
        const CONCURRENT_PORTS = 100;
        
        const ADDRESS_PREFIX = 'cr';

        public function __construct()
        {

                $this->context = new ZMQContext();
                $this->identity = self::ADDRESS_PREFIX . getmypid();
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
                $this->poll->add($this->sockets['websocket'], ZMQ::POLL_IN);

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
                                $this->clientRequests[$zmsg->address()] = new request($zmsg, $this);
                        }
                        foreach ($this->ffmpegs as $port => $process)
                        {
                                if (!$process->status())
                                {
                                        $cmd = cmd::create($this->identity, requestCmd::REMOVE_PORT, ['port' => $port]);
                                        $cmd->send($this->sockets['vSync']);
                                }
                        }
                        
                }
        }
        
        public function nextPort()
        {
                return array_search(1, $this->ports);
        }
        
        public function startFeed($id, $port)
        {
                if (!is_numeric($id) || !($this->feeds[$id] = feed::fromID($id)))
                {
                        throw new Exception("Couldn't start feed");
                }
                $this->ports[$port] = 0;
                $this->feeds[$id]->port = $port;
                $this->feeds[$id]->update();
        }
        
        public function reactivatePort($port)
        {
                $this->ports[$port] = 1;
                unset($this->ffmpegs[$port]);
        }
        
        public function stopFeed($id)
        {
                if (!is_numeric($id) || empty($this->feeds[$id]))
                {
                        throw new Exception("Couldn't stop feed");
                }
                $this->stopFFMPEG($this->feeds[$id]->port);
        }
        
        public function stopFFMPEG($port)
        {
                if (!empty($port) && isset($this->ffmpegs[$port]) && $this->ffmpegs[$port]->status())
                {
                        return $this->ffmpegs[$port]->stop();
                }
                return false;
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