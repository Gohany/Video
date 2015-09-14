<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

require_once 'zmsg.php';
require_once 'includes.php';

$proxy = new zmqSync();
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
                
                $this->identity = 'vs' . getmypid();
                
                $this->context = new ZMQContext();
                
                $this->backend = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->backend->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");
                //$this->registerBackend(zmqPorts::DEFAULT_STREAM_PORT);

                $this->frontend = new ZMQSocket($this->context, ZMQ::SOCKET_XPUB);
                $this->frontend->bind(zmqPorts::PROXY_PORT_PROTOCOL . "://*:" . zmqPorts::PROXY_PORT);
                $this->frontend->setSockOpt(ZMQ::SOCKOPT_XPUB_VERBOSE, 1);

               

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
        
        public function disconnectBackend($port, $ip = '127.0.0.1', $protocol = zmqPorts::DEFAULT_STREAM_PROTOCOL)
        {
                $networkString = trim($protocol) . '://' . trim($ip) . ':' . trim($port);
                
                if (!in_array($networkString, $this->backends))
                {
                        return true;
                }
                
                print "NETWORK STRING: " . $networkString . PHP_EOL;
                if (in_array($networkString, $this->backends) && $this->backend->disconnect($networkString))
                {
                        unset($this->backends[array_search($networkString, $this->backends)]);
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
                                                var_dump($this->zmsg);
                                                $controllerRequest = new controllerAction($this->zmsg, $this);
                                                //$controllerRequest->subscribe($this);
                                        }
                                }
                        }
                }
        }

}
