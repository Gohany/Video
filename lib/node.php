<?php

class nodes
{
        
        const DB = 'db1';
        
        public $nodes;
        
        public function __construct($nodes)
        {
                $this->nodes = $nodes;
        }
        
        public static function byActive($limit = 50, $offset = 0)
        {
                if ($statement = db::prepare(self::DB, nodeQueries::BY_ACTIVE))
                {
                        //$statement->bindParam(':limit', $limit, PDO::PARAM_INT);
                        //$statement->bindParam(':offset', $offset, PDO::PARAM_INT);
                        if ($statement->execute())
                        {
                                if ($statement->rowCount() == 0)
                                {
                                        return false;
                                }
                                $result = $statement->fetchAll(PDO::FETCH_CLASS, "node");
                                return new nodes($result);
                        }
                }
        }
        
}

class node
{
        
        public $macAddress;
        public $system_number;
        public $ip;
        public $subnet;
        public $last_ping;
        public $active;
        public $created;
        
        const DB = 'db1';
        
        public function __construct($data = array())
        {
                if (!empty($data))
                {
                        foreach ($data as $property => $value)
                        {
                                if (property_exists('node', $property))
                                {
                                        $this->{$property} = $value;
                                }
                        }
                }
        }
        
        public static function fromSystemNumber($number)
        {
                if ($statement = db::prepare(self::DB, nodeQueries::FROM_SYSTEM_NUMBER))
                {
                        $statement->execute(array(':system_number' => $number));
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        return new node($result);
                }
        }
        
        public static function fromMacAddress($macAddress)
        {
                if ($statement = db::prepare(self::DB, nodeQueries::FROM_MAC_ADRESS))
                {
                        $statement->execute(array(':macAddress' => $macAddress));
                        if ($statement->rowCount() == 1)
                        {
                                $result = $statement->fetch(PDO::FETCH_ASSOC);
                                return new node($result);
                        }
                        else
                        {
                                return false;
                        }
                }
                return false;
        }
        
        public static function fromNew($ip, $subnet, $macAddress)
        {
                if ($statement = db::prepare(self::DB, nodeQueries::INSERT))
                {
                        if ($statement->execute(array(
                                ':ip' => $ip,
                                ':subnet' => $subnet,
                                ':macAddress' => $macAddress,
                                ':last_ping' => time(),
                                ':active' => '1',
                        )))
                        {
                                return node::fromMacAddress($macAddress);
                        }
                }
        }
        
        public function setSystemNumber($number, $macAddress)
        {
                if ($statement = db::prepare(self::DB, nodeQueries::SET_SYSTEM_NUMBER))
                {
                        print "inside prepare.. " . PHP_EOL;
                        if ($this->macAddress == $macAddress)
                        {
                                $this->system_number = $number;
                        }
                        return $statement->execute(array(
                                ':system_number' => $number,
                                ':macAddress' => $macAddress,
                        ));
                }
        }
        
        public function setActive($ip, $subnet)
        {
                if ($statement = db::prepare(self::DB, nodeQueries::SET_ACTIVE))
                {
                        
                        $this->last_ping = time();
                        $this->ip = $ip;
                        $this->subnet = $subnet;
                        
                        return $statement->execute(array(
                                ':macAddress' => $this->macAddress,
                                ':ip' => $this->ip,
                                ':subnet' => $this->subnet,
                                ':last_ping' => $this->last_ping,
                                ':active' => 1,
                        ));
                        
                }
        }
        
        public function setInactive()
        {
                if ($statement = db::prepare(self::DB, nodeQueries::SET_INACTIVE))
                {
                        return $statement->execute(array(
                                ':macAddress' => $this->macAddress,
                        ));
                }
        }
        
}

class nodeQueries
{
        const SET_SYSTEM_NUMBER = "UPDATE video.activeNodes SET system_number = :system_number WHERE macAddress = :macAddress LIMIT 1";
        const INSERT = "INSERT INTO video.activeNodes (ip, subnet, macAddress, last_ping, active) VALUES (INET_ATON(:ip), INET_ATON(:subnet), :macAddress, :last_ping, :active)";
        const FROM_MAC_ADRESS = 'SELECT INET_NTOA(ip) AS ip, INET_NTOA(subnet) AS subnet, macAddress, last_ping, system_number, active, created FROM video.activeNodes WHERE macAddress = :macAddress LIMIT 1';
        const FROM_SYSTEM_NUMBER = 'SELECT INET_NTOA(ip) AS ip, INET_NTOA(subnet) AS subnet, macAddress, last_ping, system_number, active, created FROM video.activeNodes WHERE system_number = :system_number LIMIT 1';
        const BY_ACTIVE = "SELECT INET_NTOA(ip) AS ip, INET_NTOA(subnet) AS subnet, macAddress, last_ping, system_number, active, created FROM video.activeNodes WHERE active = '1'";
        //const BY_ACTIVE = 'SELECT system_id, INET_NTOA(activeNodes.ip) AS ip, INET_NTOA(subnet) AS subnet, system_number, activeSystems.active, activeNodes.created, last_ping FROM video.activeSystems JOIN video.activeNodes USING(ip) WHERE activeNodes.active = \'1\' ORDER BY system_id ASC LIMIT :offset, :limit';
        const SET_ACTIVE = "INSERT INTO video.activeNodes (ip, subnet, macAddress, last_ping, active) VALUES (INET_ATON(:ip), INET_ATON(:subnet), :macAddress, :last_ping, :active) ON DUPLICATE KEY UPDATE last_ping = values(last_ping), active = values(active), ip = values(ip), subnet = values(subnet)";
        const SET_INACTIVE = "INSERT INTO video.activeNodes (macAddress, active) VALUES (:macAddress, '0') ON DUPLICATE KEY UPDATE active = values(active)";
}