<?php

class discovery
{

        public $connected = [];
        public $socket;
        public $port = 4444;
        public $subnet = '255.255.255.255';
        public $pingIn;
        public $pingOut;
        public $macAddress;
        public $nodes;
        public $localIP;
        
        public $context;
        
        const SLEEP_TIME = 500000;
        const IP_LENGTH = 12;
        const MAC_ADDRESS_LENGTH = 17;
        const RUN_FOR_SECONDS = 59;
        const NETWORK_TIMEOUT_CHECK = 10;
        const MAX_NETWORK_TIMEOUT = 12;
        
        const CMD_CHECK_DC = '1';

        public function __construct()
        {
                
                $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                socket_set_option($this->socket, SOL_SOCKET, SO_BROADCAST, 1);
                socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 200000));
                $binded = socket_bind($this->socket, '0.0.0.0', $this->port);
                
                $this->localIP = ifconfig::ip();
                //$this->subnet = ifconfig::subnet();
                $this->macAddress = ifconfig::macAddress();
                $this->identity = $this->identity($this->macAddress);
                
                $this->context = new ZMQContext();
                $this->pingIn = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->pingIn->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");
                $this->pingOut = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                $this->pingOut->bind(zmqPorts::NETWORK_DISCOVERY_PROTOCOL . '://' . $this->localIP . ':' . zmqPorts::NETWORK_DISCOVERY_PORT_OUT);
                print "ZMQ BINDING TO: " . zmqPorts::NETWORK_DISCOVERY_PROTOCOL . '://' . $this->localIP . ':' . zmqPorts::NETWORK_DISCOVERY_PORT_OUT;
                //$this->pingIn->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->identity);
                $this->poll = new ZMQPoll();
                $this->poll->add($this->pingIn, ZMQ::POLL_IN);
                
        }
        
        public function broadcast()
        {
                print "SENDING: " . $this->identity . PHP_EOL . PHP_EOL;
                socket_sendto($this->socket, $this->identity, self::MAC_ADDRESS_LENGTH, 0, $this->subnet, $this->port);
        }
        
        public function listenUDP()
        {
                socket_recvfrom($this->socket, $buffer, self::MAC_ADDRESS_LENGTH, MSG_WAITALL, $from, $this->port);
                print "BUFFER: " . $buffer . PHP_EOL;
                print "FROM: ". $from . PHP_EOL . PHP_EOL;
                if (!empty($buffer) && !empty($from))
                {
                        $this->parseIdentity($buffer, $from);
                }
        }
        
        public function listenTCP()
        {
                $read = $write = array();
                $this->poll->poll($read, $write, 200);
                foreach ($read as $socket)
                {
                        switch ($socket)
                        {
                                case $this->pingIn:
                                        $zmsg = new Zmsg($socket);
                                        $zmsg->recv();
                                        if ($zmsg->parts() == 2)
                                        {
                                                list($macAddress, $ip) = $zmsg->extract();
                                                $this->connect($macAddress, $ip);
                                                print "GOT TCP IP: " . $ip . PHP_EOL;
                                        }
                                        elseif ($zmsg->parts() == 3)
                                        {
                                                list($macAddress, $command, $subject) = $zmsg->extract();
                                                $this->question($command, $subject);
                                        }
                                        break;
                                case $this->pingOut:
                                        $zmsg = new Zmsg($socket);
                                        $zmsg->recv();
                                        var_dump($zmsg);
                                        break;
                                default:
                                        print "NO MATCHING SOCKETS.. " . PHP_EOL;
                        }
                }
        }
        
        public function identity($macAddress)
        {
                return str_pad($macAddress, self::MAC_ADDRESS_LENGTH, ' ', STR_PAD_LEFT);// . str_pad(sprintf("%u", ip2long($ip)), self::IP_LENGTH, ' ', STR_PAD_LEFT);
        }
        
        public function question($command, $subject)
        {
                switch ($command)
                {
                        case self::CMD_CHECK_DC:
                                if ($subject == $this->macAddress)
                                {
                                        $zmsg = new Zmsg($this->pingOut);
                                        $zmsg->set($this->macAddress, $this->localIP);
                                        $zmsg->send();
                                }
                                break;
                }
        }
        
        public function connected($macAddress)
        {
                if ($macAddress == $this->macAddress)
                {
                        return true;
                }
                elseif (isset($this->connected[$macAddress]) && (time() - $this->connected[$macAddress]->last_ping) < self::MAX_NETWORK_TIMEOUT)
                {
                        return true;
                }
                return false;
        }
        
        public function upToDate($macAddress)
        {
                if ($macAddress == $this->macAddress)
                {
                        return true;
                }
                elseif (isset($this->connected[$macAddress]) && (time() - $this->connected[$macAddress]->last_ping) < self::NETWORK_TIMEOUT_CHECK)
                {
                    return true;
                }
                return false;
        }
        
        public function disconnected($macAddress)
        {
                print "DISCONNECTED: " . $macAddress . PHP_EOL;
                $this->connected[$macAddress]->setInactive();
                unset($this->connected[$macAddress]);
                return true;
        }
        
        public function connect($macAddress, $ip)
        {
                
                if (!isset($this->connected[$macAddress]))
                {
                        $this->pingIn->connect(zmqPorts::NETWORK_DISCOVERY_PROTOCOL . '://' . $ip . ':' . zmqPorts::NETWORK_DISCOVERY_PORT_IN);
                        if (!($this->connected[$macAddress] = node::fromMacAddress($macAddress)))
                        {
                                print "CREATING NEW ENTRY.. " . PHP_EOL;
                                if (!($this->connected[$macAddress] = node::fromNew($ip, $this->subnet, $macAddress)))
                                {
                                        print "... couldn't create" . PHP_EOL;
                                        return false;
                                }
                        }
                }
                
                if ($this->connected[$macAddress]->setActive($ip, $this->subnet))
                {
                        return true;
                }
                return false;
        }
        
        public function parseIdentity($buffer, $ip)
        {
                
                $macAddress = trim($buffer);
                $this->connect($macAddress, $ip);
                print "BUFFER: " . $buffer . PHP_EOL;
                print "IP: " . $ip . PHP_EOL;
                
        }
        
        public function checkConnections()
        {
                print "checking.. " . PHP_EOL;
                foreach ($this->connected as $macAddress => $node)
                {
                        if (!$this->connected($macAddress))
                        {
                                $this->disconnected($macAddress);
                        }
                        elseif (!$this->upToDate($macAddress))
                        {
                                print "CHECKING CONNECTION... " . PHP_EOL;
                                // ping the network
                                $zmsg = new Zmsg($this->pingOut);
                                $zmsg->set($this->macAddress, self::CMD_CHECK_DC, $macAddress);
                                var_dump($zmsg);
                                $zmsg->send();
                        }
                }
        }
        
        public function run()
        {
                while(true)
                {       
                        $this->broadcast();
                        if (count($this->connected) > 0)
                        {
                                for ($c=count($this->connected), $i=0; $i<$c; $i++)
                                {
                                        $this->listenUDP();
                                        $this->listenTCP();
                                }
                        }
                        else
                        {
                                $this->listenUDP();
                                $this->listenTCP();
                        }
                        $this->checkConnections();
                        usleep(self::SLEEP_TIME);
                }
                return true;
        }

}
