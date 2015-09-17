<?php

class session
{
        
        public $redis;
        public $sessionId;
        
        const REDIS_SESSION_PREFIX = "PHPREDIS_SESSION:";
        const REDIS_IP = '127.0.0.1';
        const REDIS_PORT = '6379';
        const REDIS_TIMEOUT = 100;
        
        public function __construct($sid)
        {
                if (!empty($sid))
                {
                        session_start();

                        $this->sessionId = $sid;
                        $this->redis = new Redis();

                        try
                        {
                                $this->redis->connect(self::REDIS_IP, self::REDIS_PORT, self::REDIS_TIMEOUT);
                        }
                        catch (RedisException $ex)
                        {
                                throw new Exception('Could not connect to redis.');
                        }
                        
                        $this->loadSessionData();
                        
                }
        }
        
        public function loadSessionData()
        {
                return session_decode($this->redis->get(self::REDIS_SESSION_PREFIX . $this->sessionId));
        }

}