<?php

// make it a one table thing.... add last ping and make IP a unique key, add mac address

class systems
{
        
        const DB = 'db1';
        
        public $systems;
        
        public function __construct($systems)
        {
                $this->systems = $systems;
        }
        
        public static function byLatest($limit = 50, $offset = 0)
        {
                if ($statement = db::prepare(self::DB, systemQueries::BY_LATEST))
                {
                        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
                        if ($statement->execute())
                        {
                                if ($statement->rowCount() == 0)
                                {
                                        return false;
                                }
                                $result = $statement->fetchAll(PDO::FETCH_CLASS, "system");
                                return new feeds($result);
                        }
                }
        }
        
}

class system
{
        
        public $system_id;
        public $system_number;
        public $ip;
        public $active;
        public $created;
        
        const DB = 'db1';
        
        public function __construct($data = array())
        {
                if (!empty($data))
                {
                        foreach ($data as $property => $value)
                        {
                                if (property_exists('system', $property))
                                {
                                        $this->{$property} = $value;
                                }
                        }
                }
        }
        
        public static function fromSystemNumber($number)
        {
                if ($statement = db::prepare(self::DB, systemQueries::FROM_SYSTEM_NUMBER))
                {
                        $statement->execute(array(':system_number' => $number));
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        return new system($result);
                }
        }
        
        public static function fromID($id)
        {
                if ($statement = db::prepare(self::DB, systemQueries::FROM_ID))
                {
                        $statement->execute(array(':system_id' => $id));
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        return new system($result);
                }
        }
        
        public static function fromIP($ip)
        {
                if ($statement = db::prepare(self::DB, systemQueries::FROM_IP))
                {
                        $statement->execute(array(':ip' => $ip));
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        return new system($result);
                }
        }
        
        public function update()
        {
                if ($statement = db::prepare(self::DB, systemQueries::UPDATE_BY_ID))
                {
                        return $statement->execute(array(
                                        ':system_id' => $this->system_id,
                                        ':ip' => $this->ip,
                                        ':system_number' => $this->system_number,
                                        ':active' => $this->active,
                                ));
                }
        }
        
        public function insert()
        {
                if ($statement = db::prepare(self::DB, systemQueries::INSERT))
                {
                        $statement->execute(array(
                                ':ip' => $this->ip,
                                ':system_number' => $this->system_number,
                                ':active' => $this->active,
                        ));
                        $this->system_id = db::lastInsertId(self::DB);
                        return true;
                }
        }
        
        public function setActive()
        {
                $this->active = 1;
                return $this->update();
        }
        
        public function setInactive()
        {
                $this->active = 0;
                return $this->update();
        }
        
}

class systemQueries
{
        const BY_LATEST = 'SELECT system_id, INET_NTOA(ip) AS ip, system_number, active, created FROM video.activeSystems ORDER BY system_id ASC LIMIT :offset, :limit';
        const FROM_SYSTEM_NUMBER = 'SELECT system_id, INET_NTOA(ip) AS ip, system_number, active, created FROM video.activeSystems WHERE system_number = :system_number LIMIT 1';
        const FROM_IP = 'SELECT system_id, INET_NTOA(ip) AS ip, system_number, active, created FROM video.activeSystems WHERE ip = INET_ATON(:ip) LIMIT 1';
        const FROM_ID = 'SELECT system_id, INET_NTOA(ip) AS ip, system_number, active, created FROM video.activeSystems WHERE system_id = :system_id LIMIT 1';
        const UPDATE_BY_ID = "UPDATE video.activeSystems SET ip = INET_ATON(:ip), active = :active, system_number = :system_number WHERE system_id = :system_id LIMIT 1";
        const UPDATE_BY_IP = "UPDATE video.activeSystems SET system_number = :system_number, active = :active WHERE ip = INET_ATON(:ip) LIMIT 1";
        const INSERT = "INSERT INTO video.activeSystems (ip, system_number, active) VALUES (INET_ATON(:ip), :system_number, :active)";
}