<?php

class discovery
{

        public $connected = [];
        public $socket;
        public $port = 4444;
        public $subnet = '255.255.255.255';
        public $pingIn;
        public $pingOut;
        
        public $context;
        
        const SLEEP_TIME = 500000;
        const IDENTITY_LENGTH = 12;
        const RUN_FOR_SECONDS = 59;
        const NETWORK_TIMEOUT_CHECK = 10;
        const MAX_NETWORK_TIMEOUT = 12;
        
        const CMD_CHECK_DC = '1';
        
        const DB = 'db1';
        const SET_ACTIVE_MYSQL = "INSERT INTO video.activeNodes (ip, subnet, last_ping, active) VALUES (INET_ATON(:ip), INET_ATON(:subnet), :last_ping, :active) ON DUPLICATE KEY UPDATE last_ping = values(last_ping), active = values(active)";
        const SET_INACTIVE_MYSQL = "INSERT INTO video.activeNodes (ip, subnet, active) VALUES (INET_ATON(:ip), INET_ATON(:subnet), '0') ON DUPLICATE KEY UPDATE active = values(active)";

        public function __construct()
        {
                
                $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                socket_set_option($this->socket, SOL_SOCKET, SO_BROADCAST, 1);
                socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => 0, "usec" => 200000));
                $binded = socket_bind($this->socket, '0.0.0.0', $this->port);
                
                $process = popen("ifconfig | grep 'inet addr:192'", 'r');
                $line = trim(fread($process, 1024));
                pclose($process);
                preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $matches);
                
                $this->localIP = $matches[0];
                $this->identity = $this->identity($this->localIP);
                //$this->subnet = $matches[2];
                
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
                socket_sendto($this->socket, $this->identity, self::IDENTITY_LENGTH, 0, $this->subnet, $this->port);
        }
        
        public function listenUDP()
        {
                socket_recvfrom($this->socket, $buffer, self::IDENTITY_LENGTH, MSG_WAITALL, $from, $this->port);
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
                                        $ip = $zmsg->address();
                                        if ($zmsg->parts() == 1)
                                        {
                                                $this->connect($ip);
                                                print "GOT TCP IP: " . $ip . PHP_EOL;
                                        }
                                        elseif ($zmsg->parts() == 3)
                                        {
                                                list($ip, $command, $subject) = $zmsg->extract();
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
        
        public function identity($ip)
        {
                return str_pad(sprintf("%u", ip2long($ip)), self::IDENTITY_LENGTH, ' ', STR_PAD_LEFT);
        }
        
        public function question($command, $subject)
        {
                switch ($command)
                {
                        case self::CMD_CHECK_DC:
                                if ($subject == $this->localIP)
                                {
                                        $zmsg = new Zmsg($this->pingOut);
                                        $zmsg->set($this->localIP);
                                        $zmsg->send();
                                }
                                break;
                }
        }
        
        public function connected($ip)
        {
                if ($ip == $this->localIP)
                {
                        return true;
                }
                elseif (isset($this->connected[$ip]) && (time() - $this->connected[$ip]) < self::MAX_NETWORK_TIMEOUT)
                {
                        return true;
                }
                return false;
        }
        
        public function upToDate($ip)
        {
                if ($ip == $this->localIP)
                {
                        return true;
                }
                elseif (isset($this->connected[$ip]) && (time() - $this->connected[$ip]) < self::NETWORK_TIMEOUT_CHECK)
                {
                    return true;
                }
                return false;
        }
        
        public function disconnected($ip)
        {
                print "DISCONNECTED: " . $ip . PHP_EOL;
                unset($this->connected[$ip]);
                
                if ($statement = db::prepare(self::DB, self::SET_INACTIVE_MYSQL))
                {
                        $statement->execute(array(
                                ':ip' => $ip,
                                ':subnet' => $this->subnet,
                        ));
                        return true;
                }
                return true;
        }
        
        public function connect($ip)
        {
                if (!isset($this->connected[$ip]))
                {
                        $this->pingIn->connect(zmqPorts::NETWORK_DISCOVERY_PROTOCOL . '://' . $ip . ':' . zmqPorts::NETWORK_DISCOVERY_PORT_IN);
                }

                $this->connected[$ip] = time();
                
                if ($statement = db::prepare(self::DB, self::SET_ACTIVE_MYSQL))
                {
                        $statement->execute(array(
                                ':ip' => $ip,
                                ':subnet' => $this->subnet,
                                ':last_ping' => time(),
                                ':active' => 1,
                        ));
                        return true;
                }
                return true;
        }
        
        public function parseIdentity($buffer, $from)
        {
                $bufferIP = long2ip((float) trim($buffer));
                $fromLong = $this->identity($from);
                
                if ($buffer == $fromLong)
                {
                        $this->connect($from);
                        print "BUFFER: " . $buffer . PHP_EOL;
                        print "LONG2IP: " . $bufferIP . PHP_EOL;
                        print "FROM: " . $from . PHP_EOL;
                        print "FROMLONG: " . $fromLong . PHP_EOL . PHP_EOL;
                }
                
        }
        
        public function checkConnections()
        {
                print "checking.. " . PHP_EOL;
                foreach ($this->connected as $ip => $time)
                {
                        if (!$this->connected($ip))
                        {
                                $this->disconnected($ip);
                        }
                        elseif (!$this->upToDate($ip))
                        {
                                print "CHECKING CONNECTION... " . PHP_EOL;
                                // ping the network
                                $zmsg = new Zmsg($this->pingOut);
                                $zmsg->set($this->localIP, self::CMD_CHECK_DC, $ip);
                                var_dump($zmsg);
                                $zmsg->send();
                        }
                }
        }
        
        public function run()
        {
                while(true)
                {
//                        if ((time() - $_SERVER['REQUEST_TIME']) >= self::RUN_FOR_SECONDS)
//                        {
//                                break;
//                        }
                        
                        // listen (number of connections) times longer than broadcasting
                        
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
