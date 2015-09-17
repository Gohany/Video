<?php

class feeds
{
        
        const DB = 'db1';
        
        public $feeds;
        
        public function __construct($feeds)
        {
                $this->feeds = $feeds;
        }
        
        public static function byLatest($limit, $offset = 0)
        {
                if ($statement = db::prepare(self::DB, feedQueries::BY_LATEST))
                {
                        $statement->bindParam(':limit', $limit, PDO::PARAM_INT);
                        $statement->bindParam(':offset', $offset, PDO::PARAM_INT);
                        if ($statement->execute())
                        {
                                if ($statement->rowCount() == 0)
                                {
                                        return false;
                                }
                                $result = $statement->fetchAll(PDO::FETCH_CLASS, "feed");
                                return new feeds($result);
                        }
                }
        }
        
}

class feed
{
        
        const DB = 'db1';
        
        public $feed_id;
        public $name;
        public $input;
        public $port;
        public $pid;
        public $active;
        public $ended;
        public $started;
        public $created;
        
        public function __construct($data = array())
        {
                if (!empty($data))
                {
                        foreach ($data as $property => $value)
                        {
                                if (property_exists('feed', $property))
                                {
                                        $this->{$property} = $value;
                                }
                        }
                }
        }
        
        public static function fromID($id)
        {
                if ($statement = db::prepare(self::DB, feedQueries::FROM_ID))
                {
                        $statement->execute(array(':feed_id' => $id));
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        return new feed($result);
                }
        }
        
        public static function fromInput($input)
        {
                if ($statement = db::prepare(self::DB, feedQueries::FROM_INPUT))
                {
                        $statement->execute(array(':input' => $input));
                        $result = $statement->fetch(PDO::FETCH_ASSOC);
                        return new feed($result);
                }
        }
        
        public function update()
        {
                if ($statement = db::prepare(self::DB, feedQueries::UPDATE))
                {
                        return $statement->execute(array(
                                        ':feed_id' => $this->feed_id,
                                        ':port' => $this->port,
                                        ':pid' => $this->pid,
                                        ':active' => $this->active,
                                        ':ended' => $this->ended,
                                        ':started' => $this->started,
                                ));
                }
        }
        
        public function startFeed($pid)
        {
                if ($statement = db::prepare(self::DB, feedQueries::START_FEED))
                {
                        return $statement->execute(array(
                                        ':pid' => $pid,
                                        ':feed_id' => $this->feed_id,
                                ));
                }
        }
        
        public function insert()
        {
                if ($statement = db::prepare(self::DB, feedQueries::INSERT))
                {
                        $statement->execute(array(
                                ':name' => $this->name,
                                ':input' => $this->input,
                                ':port' => $this->port,
                                ':pid' => $this->pid,
                                ':active' => $this->active,
                                ':ended' => $this->ended,
                                ':started' => $this->started,
                        ));
                        $this->feed_id = db::lastInsertId(self::DB);
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

class feedQueries
{
        const START_FEED = "UPDATE video.feeds SET active = '1', pid = :pid, started = NOW() WHERE feed_id = :feed_id LIMIT 1";
        const BY_LATEST = 'SELECT * FROM video.feeds ORDER BY feed_id ASC LIMIT :offset, :limit';
        const FROM_ID = 'SELECT * FROM video.feeds WHERE feed_id = :feed_id LIMIT 1';
        const FROM_INPUT = 'SELECT * FROM video.feeds WHERE input = :input LIMIT 1';
        const SET_ACTIVE = "UPDATE video.feeds SET active = '1' WHERE feed_id = :feed_id LIMIT 1";
        const SET_INACTIVE = "UPDATE video.feeds SET active = '0' WHERE feed_id = :feed_id LIMIT 1";
        const UPDATE = "UPDATE video.feeds SET port = :port, pid = :pid, active = :active, ended = :ended, started = :started WHERE feed_id = :feed_id LIMIT 1";
        const INSERT = "INSERT INTO video.feeds (name, input, port, pid, active, ended, started) VALUES (:name, :input, :port, :pid, :active, :ended, :started)";
}