<?php

try
{
        $clientVideo = new clientVideo;
        $clientVideo->run();
}
catch (Exception $e)
{
        print "Exception: " . $e->getMessage() . PHP_EOL;
}

class clientVideo
{
        
        public $videoID;
        public $sessionId;
        public $redis;
        public $videoData;
        
        const REDIS_SESSION_PREFIX = "PHPREDIS_SESSION:";
        const REDIS_IP = '127.0.0.1';
        const REDIS_PORT = '6379';
        const REDIS_TIMEOUT = 100;
        
        public function __construct()
        {
                
                session_start();
                $this->videoID = !empty($_GET['id']) ? $_GET['id'] : 1;
                $this->sessionId = !empty($_GET['sid']) ? $_GET['sid'] : null;
                $this->redis = new Redis();
                
                try
                {
                        $this->redis->connect(self::REDIS_IP, self::REDIS_PORT, self::REDIS_TIMEOUT);
                }
                catch (RedisException $ex)
                {
                        throw new Exception('Could not connect to redis.');
                }
                
                if (!empty($this->sessionId) && $this->loadSessionData())
                {
                        $this->videoData = new clientVideoData($this->videoID);
                }
                
        }
        
        public function run()
        {
                while (true)
                {
                        if (!$this->videoData->run())
                        {
                                return false;
                        }
                }
        }
        
        public function loadSessionData()
        {
                return session_decode($this->redis->get(self::REDIS_SESSION_PREFIX . $this->sessionId));
        }
        
}

class clientVideoData
{
        
        public $containerSent = false;
        public $context;
        public $videoSubscription;
        public $commandSubscription;
        public $id;
        public $headerString;
        public $subscriptions = [
            'video' => [],
            'command' => [],
        ];
        
        const PROXY_PORT = 8100;
        const COMMAND_PORT = 8101;
        const VIDEO_PREFIX = 'mkv.';
        const VIDEO_HEADERS_DIR = '/var/www/';
        
        const CMD_CHANGE_CHANNEL = 'cc';
        const CMD_STOP = 'stop';
        const CMD_ADD_CHANNEL = 'add';
        const CMD_REMOVE_CHANNEL = 'del';
        
        public function __construct($id = 1)
        {
                
                $this->id = $id;
                $this->context = new ZMQContext;
                $this->videoSubscription = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->videoSubscription->connect("tcp://localhost:" . self::PROXY_PORT);
                $this->subscribe('video', 'mkv.' . $this->id);
                
                $this->commandSubscription = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->commandSubscription->connect("tcp://localhost:" . self::COMMAND_PORT);
                $this->subscribe('command', 'all');
                
                $this->poll = new ZMQPoll();
                $this->poll->add($this->videoSubscription, ZMQ::POLL_IN);
                $this->poll->add($this->commandSubscription, ZMQ::POLL_IN);
                
                $this->headerString = self::VIDEO_HEADERS_DIR . self::VIDEO_PREFIX . $this->id . '.header';
                $this->header();
                
        }
        
        public function subscribe($socket, $subscription)
        {
                if (!empty($this->{$socket.'Subscription'}))
                {
                        
                        try
                        {
                                 $this->{$socket.'Subscription'}->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $subscription);
                        }
                        catch (ZMQSocketException $ex)
                        {
                                throw new Exception ('Failed to subscribe to ' . $subscription . ' on socket ' . $socket);
                                return false;
                        }
                       
                        $this->subscriptions[$socket][] = $subscription;
                        return true;
                }
                return false;
        }
        
        public function unsubscribe($socket, $subscription)
        {
                if (!empty($this->{$socket.'Subscription'}) && in_array($subscription, $this->subscriptions[$socket]))
                {
                        
                        try
                        {
                                 $this->{$socket.'Subscription'}->setSockOpt(ZMQ::SOCKOPT_UNSUBSCRIBE, $subscription);
                        }
                        catch (ZMQSocketException $ex)
                        {
                                throw new Exception ('Failed to unsubscribe to ' . $subscription . ' on socket ' . $socket);
                                return false;
                        }
                        $key = array_search($subscription, $this->subscriptions[$socket]);
                        unset($this->subscriptions[$socket][$key]);
                        return true;
                }
                return false;
        }
        
        public function run()
        {
                
                $readable = $writeable = array();
                $events = $this->poll->poll($readable, $writeable);
                if ($events > 0)
                {
                        foreach ($readable as $socket)
                        {
                                if ($socket === $this->videoSubscription)
                                {
                                        $this->printVideo();
                                        return true;
                                }
                                elseif ($socket === $this->commandSubscription)
                                {
                                        // do stuff
                                        return $this->doCommand();
                                }
                        }
                }
                return true;
        }
        
        public function doCommand()
        {
                $packet = $this->videoSubscription->recv();
                foreach ($this->subscriptions['command'] as $subscription)
                {
                        if (substr($packet, 0, strlen($subscription)) === $subscription)
                        {
                                $data = substr($packet, strlen($subscription));
                                $parts = explode($data, ' ');
                                switch ($parts[0])
                                {
                                        case self::CMD_CHANGE_CHANNEL:
                                                return $this->changeChannel($parts[1]);
                                                break;
                                        case self::CMD_STOP:
                                                return false;
                                                break;
                                        case self::CMD_ADD_CHANNEL:
                                                return $this->subscribe('video', $parts[1]);
                                                break;
                                        case self::CMD_REMOVE_CHANNEL:
                                                return $this->unsubscribe('video', $parts[1]);
                                                break;
                                }
                        }
                }
        }
        
        public function changeChannel($channel)
        {
                foreach ($this->subscriptions['video'] as $sub)
                {
                        if (!$this->unsubscribe('video', $sub))
                        {
                                return false;
                        }
                }
                $this->containerSent = false;
                
                return $this->subscribe('video', $channel);
        }
        
        public function printVideo()
        {
                $packet = $this->videoSubscription->recv();
                foreach ($this->subscriptions['video'] as $subscription)
                {
                        if (substr($packet, 0, strlen($subscription)) === $subscription)
                        {
                                $data = substr($packet, strlen($subscription));
                                if ($this->containerSent === true)
                                {
                                        print substr($data, 1);
                                }
                                elseif ($data[0] === '1')
                                {
                                        print substr($data, 1);
                                        $this->containerSent = true;
                                }
                        }
                }
        }
        
        public function waitForContainer()
        {
                $this->containerSent = false;
        }
        
        public function header()
        {
                
                $handle = fopen($this->headerString, 'r');
                do
                {
                        flock($handle, LOCK_EX | LOCK_NB, $wouldblock);
                }
                while ($wouldblock);
                flock($handle, LOCK_UN);
                
                while (!feof($handle))
                {
                        print fread($handle, 1024);
                }
        }
        
}
/*
session_start();
$id = !empty($_GET['id']) ? $_GET['id'] : 1;
$sessionId = !empty($_GET['sid']) ? $_GET['sid'] : 0;

$sessionKey = "PHPREDIS_SESSION:" . $sessionId;
//Create new connection
$redis = new Redis();
$redis->connect('127.0.0.1', 6379, 100);

$sessionData = session_decode($redis->get($sessionKey));
var_dump($_SESSION);
exit;

$context = new ZMQContext();

$subscriber = new ZMQSocket($context, ZMQ::SOCKET_SUB);
$subscriber->connect("tcp://localhost:8100");

$subscriber->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, 'mkv.' . $id);

$hasContainer = false;
//'/var/www/mkv.' . $id . '.header'
$handle = fopen('/var/www/mkv.' . $id . '.header', 'r');

do
{
        flock($handle, LOCK_EX | LOCK_NB, $wouldblock);
        //print "checking file lock..." . PHP_EOL;
}
while ($wouldblock);
//print "unlocking" . PHP_EOL;
flock($handle, LOCK_UN);

//print "unlocked" . PHP_EOL;

while (!feof($handle))
{
        print fread($handle, 1024);
}

//print "entering loop" . PHP_EOL;

while (true)
{
        //print "GOT SOMETHING" . PHP_EOL;
        $mkv = substr($subscriber->recv(), 5);
        //var_dump($mkv[0]);
        if ($hasContainer)
        {
                //print "HAVE CONTAINER" . PHP_EOL;
                print substr($mkv, 1);
        }
        elseif ($mkv[0] === '1')
        {
                $hasContainer = true;
                print substr($mkv, 1);
                //print "JUST GOT CONTAINER" . PHP_EOL;
        }
        else
        {
                //print "NO CONTAINER" . PHP_EOL;
        }
}
 */