<?php

require_once 'includes.php';

$controller = new vController;
$controller->run();

class vController
{

        public $context;
        public $clientSocket;
        public $vSyncSocket;
        public $websocketSocket;
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
                $this->clientSocket = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->vSyncSocket = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                $this->websocketSocket = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                $this->clientSocket->bind(zmqPorts::CLIENT_CONTROLLER_PROTOCOL . "://*:" . zmqPorts::CLIENT_CONTROLLER_INSTRUCTION);
                $this->vSyncSocket->bind(zmqPorts::CONTROLLER_VSYNC_PROTOCOL . "://" . zmqPorts::CONTROLLER_VSYNC_INSTRUCTION);
                $this->websocketSocket->bind(zmqPorts::CONTROLLER_WEBSOCKET_PROTOCOL . "://" . zmqPorts::CONTROLLER_WEBSOCKET_INSTRUCTION);

                //  Initialize poll set
                $this->poll = new ZMQPoll();
                $this->poll->add($this->clientSocket, ZMQ::POLL_IN);
                $this->poll->add($this->vSyncSocket, ZMQ::POLL_IN);
                $this->poll->add($this->websocketSocket, ZMQ::POLL_IN | ZMQ::POLL_OUT);
                
                $this->ports = array_fill(self::STARTING_PORT, self::CONCURRENT_PORTS, 1);
                
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
                                if ($socket === $this->clientSocket)
                                {
                                        $request = $zmsg->body();
                                        print "REQUEST: " . $request . PHP_EOL;
                                        // do mysql stuff
                                        
                                        if (!is_numeric($request) || !($this->feeds[$request] = feed::fromID($request)))
                                        {
                                                $zmsg->body_set('failure')->send();
                                                break;
                                        }
                                        
                                        $port = array_search(1, $this->ports);
                                        
                                        if ($port === false)
                                        {
                                                $zmsg->body_set('failure')->send();
                                                break;
                                        }
                                        
                                        $this->ports[$port] = 0;
                                        $this->feeds[$request]->port = $port;
                                        $this->feeds[$request]->update();
                                        
                                        print "PORT: " . $port . PHP_EOL;
                                        $zmsg->push($request);
                                        $zmsg->push($port);
                                        $zmsg->wrap(1234);
                                        //$zmsg->body_set($port);
                                        //$zmsg->push($request);
                                        $zmsg->set_socket($this->vSyncSocket)->send();
                                        
                                }
                                elseif ($socket === $this->vSyncSocket)
                                {
                                        // if good.. update mysql
                                        // start container logic
                                        // stuff
                                        var_dump($zmsg);
                                        $address = $zmsg->unwrap();
                                        print "ADDRESS: ".$address . PHP_EOL;
                                        $id = $zmsg->pop();
                                        print "ID: ".$id.PHP_EOL;
                                        $reply = $zmsg->body();
                                        print "REPLY: " . $reply . PHP_EOL;
                                        // update mysql
                                        if (!empty($this->feeds[$id]) && ($pid = $this->startFFMPEG($this->feeds[$id]->input, $id, $this->feeds[$id]->port)))
                                        {
                                                
                                                $this->feeds[$id]->startFeed($pid);
                                                print "PORT: " . $this->feeds[$id]->port . PHP_EOL;
                                                //$this->startingPort++;

                                                $zmsg->body_set('success');
                                                $zmsg->set_socket($this->clientSocket)->send();
                                        }
                                        else
                                        {
                                                $this->ports[$this->feeds[$id]->port] = 1;
                                                $zmsg->body_set('failure');
                                                $zmsg->set_socket($this->clientSocket)->send();
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
