<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

require_once 'zmsg.php';
require_once 'includes.php';

$proxy = new zmqSync();
//$proxy->registerBackend('5556');
$proxy->run();

class zmqSync
{

        public $context;
        public $frontend;
        public $backend;
        public $backends = array();
        public $poll;
        public $instructionService;
        public $cache;
        public $syncService;

        public function __construct()
        {
                $this->context = new ZMQContext();

                $this->backend = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->registerBackend(zmqPorts::DEFAULT_STREAM_PORT);

                $this->frontend = new ZMQSocket($this->context, ZMQ::SOCKET_XPUB);
                $this->frontend->bind(zmqPorts::PROXY_PORT_PROTOCOL . "://*:" . zmqPorts::PROXY_PORT);
                $this->frontend->setSockOpt(ZMQ::SOCKOPT_XPUB_VERBOSE, 1);

                $this->backend->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");

                $this->instructionService = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                
                $this->instructionService->connect(zmqPorts::CONTROLLER_VSYNC_PROTOCOL . "://" . zmqPorts::CONTROLLER_VSYNC_INSTRUCTION);
                
                $this->syncService = new ZMQSocket($this->context, ZMQ::SOCKET_REP);
                $this->syncService->bind("tcp://*:" . zmqPorts::VSYNC_WEBSOCKET_INSTRUCTION);

                $this->poll = new ZMQPoll();
                $this->poll->add($this->backend, ZMQ::POLL_IN);
                $this->poll->add($this->frontend, ZMQ::POLL_IN);
                $this->poll->add($this->instructionService, ZMQ::POLL_IN);
                $this->poll->add($this->syncService, ZMQ::POLL_IN);
                
                $this->zmsg = new Zmsg($this->instructionService);
        }

        public function registerBackend($port, $ip = '127.0.0.1', $protocol = zmqPorts::DEFAULT_STREAM_PROTOCOL)
        {
                $networkString = trim($protocol) . '://' . trim($ip) . ':' . trim($port);
                if (!in_array($networkString, $this->backends) && $this->backend->connect($networkString))
                {
                        $this->backends[] = $networkString;
                        print_r($this->backends);
                        return true;
                }
                return false;
        }

        public function run()
        {
                $readable = $writeable = array();
                while (true)
                {

                        $events = $this->poll->poll($readable, $writeable);
                        if ($events > 0)
                        {
                                
                                foreach ($readable as $socket)
                                {
                                        if ($socket === $this->backend)
                                        {
                                                //  Process all parts of the message
                                                $this->cache[0] = $this->backend->recv();
                                                //  Multipart detection
                                                //$more = $this->backend->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
                                                //$this->frontend->send($this->cache, $more ? ZMQ::SOCKOPT_SNDMORE : 0);
                                                //if (!$more)
                                                //{
                                                        //break; // Last message part
                                                //}
                                                if (isset($this->cache[1]))
                                                {
                                                        $this->frontend->send($this->cache[1], 0);
                                                }
                                                $this->cache[1] = $this->cache[0];
                                        }
                                        elseif ($socket === $this->frontend)
                                        {
                                                print "SENDING CACHE!" . PHP_EOL;
                                                $message = $this->frontend->recv();
                                                var_dump($message);
//                                                if (isset($this->cache[1]))
//                                                {
//                                                        $this->frontend->send($this->cache[1]);
//                                                }
                                        }
                                        elseif ($socket === $this->syncService)
                                        {
                                                
                                                $subscription = $this->syncService->recv();
                                                print "NEW GUY: ".$subscription . PHP_EOL;
                                                $this->syncService->send('rgr');
                                                
                                                
                                        }
                                        elseif ($socket === $this->instructionService)
                                        {
                                                print "NEW PORT REQUEST" . PHP_EOL;
                                                $this->zmsg->recv();
                                                $address = $this->zmsg->unwrap();
                                                $port = $this->zmsg->pop();
                                                // do stuff
                                                $this->registerBackend($port);
                                                $this->zmsg->body_set('success')->wrap($address);
                                                $this->zmsg->send(true);
                                        }
                                }
                        }
                }
        }

}
