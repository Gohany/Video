<?php

class system
{
        
        // 1. set system number identifiers
        // 2. set configs
        // 3. 
        
        public $configs;
        
        const ROOT_DIR = '/var/www';
        const CONFIGS_DIR = '/var/www/configs';
        
        public $subscriptions;
        public $publish;
        public $subscribe;
        public $context;
        public $localIP;
        public $requests;
        public $identity;
        public $nodesToSystemNumber;
        public $processes;
        public $client;
        
//        public static $startupProcesses = [
//            PHP_BINDIR . '/php /var/www/vSync.php',
//            PHP_BINDIR . '/php /var/www/controller.php',
//        ];
        
        public static $startupProcesses = [
            'discoverNetwork.php',
            'vSync.php',
            'controller.php',
            'clientWebsocket.php',
        ];
        
        const ADDRESS_PREFIX = 'sy';
        const CHECK_NODES_SECONDS = 2;
        
        public function __construct()
        {
                
                $this->identity = self::ADDRESS_PREFIX . getmypid();
                $this->localIP = ifconfig::ip();
                $this->context = new ZMQContext();
                $this->publish = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                $this->publish->bind(zmqPorts::SYSTEM_SYNC_PROTOCOL . '://' . $this->localIP . ':' . zmqPorts::SYSTEM_SYNC_PORT);
                $this->subscribe = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->subscribe->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");
                
                $this->client = new ZMQSocket($this->context, ZMQ::SOCKET_ROUTER);
                $this->client->bind(zmqPorts::CLIENT_SYSTEM_PROTOCOL . "://*:" . zmqPorts::CLIENT_SYSTEM_INSTRUCTION);
                
                $this->poll = new ZMQPoll();
                $this->poll->add($this->subscribe, ZMQ::POLL_IN);
                $this->poll->add($this->client, ZMQ::POLL_IN);
                
                $this->startProcs();
                
        }
        
        public function startProcs()
        {
                foreach (self::$startupProcesses as $file)
                {
                        if (!Process::isRunning('php /var/www/' . $file))
                        {
                                $this->processes[$file] = new Process(PHP_BINDIR . '/php /var/www/' . $file);
                        }
                        else
                        {
                                do
                                {
                                        $processes = Process::fromCMD('php /var/www/' . $file);
                                        if (count($processes) > 1)
                                        {
                                                foreach ($processes as $pid => $process)
                                                {
                                                        $process->stop();
                                                }
                                        }
                                        elseif (count($processes == 1))
                                        {
                                                $this->processes[$file] = current($processes);
                                                break;
                                        }
                                }
                                while (Process::isRunning('php /var/www/' . $file));
                                
                                if (!isset($this->processes[$file]))
                                {
                                        $this->processes[$file] = new Process(PHP_BINDIR . '/php /var/www/' . $file);
                                }
                        }
                }
        }
        
        public function shutdownProcs()
        {
                foreach ($this->processes as $file => $process)
                {
                        $process->stop();
                }
        }
        
        public function checkNodes()
        {
                if (time() % self::CHECK_NODES_SECONDS == 0)
                {
                        $nodes = nodes::byActive();
                        foreach ($nodes->nodes as $node)
                        {
                                if (!isset($this->subscriptions[$node->ip]))
                                {
                                        $this->subscriptions[$node->ip] = time();
                                        $this->subscribe->connect(zmqPorts::SYSTEM_SYNC_PROTOCOL . '://' . $node->ip . ':' . zmqPorts::SYSTEM_SYNC_PORT);
                                }
                                $nodesByIp[$node->ip] = time();
                        }
                        foreach (array_diff_key($this->subscriptions, $nodesByIp) as $ip => $time)
                        {
                                $this->subscribe->disconnect(zmqPorts::SYSTEM_SYNC_PROTOCOL . '://' . $ip . ':' . zmqPorts::SYSTEM_SYNC_PORT);
                                unset($this->subscriptions[$ip]);
                        }
                }
                return true;
        }
        
        public function checkProcs()
        {
                foreach ($this->processes as $file => $process)
                {
                        if (!$process->status())
                        {
                                $this->processes[$file] = new Process(PHP_BINDIR . '/php /var/www/' . $file);
                        }
                }
        }
        
        public function run()
        {
                while (true)
                {
                        $this->checkProcs();
                        $this->checkNodes();
                        $this->poll();
                }
        }
        
        public function poll()
        {
                $read = $write = array();
                $this->poll->poll($read, $write, 200);
                foreach ($read as $socket)
                {
                        switch ($socket)
                        {
                                case $this->subscribe:
                                        $zmsg = new Zmsg($socket);
                                        $zmsg->recv();
                                        var_dump($zmsg);
                                        $this->requests[$zmsg->address()] = new request($zmsg, $this);
                                        break;
                                case $this->client:
                                        $zmsg = new Zmsg($socket);
                                        $zmsg->recv();
                                        var_dump($zmsg);
                                        $this->requests[$zmsg->address()] = new request($zmsg, $this);
                                        break;
                                default:
                                        print "NO MATCHING SOCKETS.. " . PHP_EOL;
                        }
                }
        }
        
        
        public static function assignSystemNumber($number, $macAddress)
        {
                
                // MAKE A CALL TO THE CONTROLLER? DO IT FROM CLIENT? 
                // MAKE A THING THAT ACTUALLY STARTS TEH CONTROLLER AND VSYNC AND DISCOVERY?? probably..
                // talk to THAT
                $nodes = nodes::byActive();
                var_dump($nodes);
                foreach ($nodes->nodes as $node)
                {
                        print "RESULT: " . PHP_EOL;
                        var_dump($node->setSystemNumber($number, $macAddress));
                }
                
//                if ($activeNode = node::fromMacAddress($macAddress))
//                {
//                        if ($activeNode->system_number == $number)
//                        {
//                                return true;
//                        }
//
//                        if ($node = node::fromSystemNumber($number))
//                        {
//                                $node->setSystemNumber(NULL);
//                        }
//
//                        return $activeNode->setSystemNumber($number);
//                }
                return true;
        }      
        
}