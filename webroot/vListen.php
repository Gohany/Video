<?php

require_once '../session.php';
require_once '../clientCommands.php';

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
        public $videoData;
        public $session;
        
        public function __construct()
        {
                
                $this->videoID = !empty($_GET['id']) ? $_GET['id'] : 1;
                $this->session = !empty($_GET['sid']) ? new session($_GET['sid']) : false;
                //if ($this->session)
                //{
                        $this->videoData = new clientVideoData($this->videoID);
                        if ($this->session)
                        {
                                $this->videoData->subscribe('command', $this->session->sessionId);
                        }
                //}
                
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
                $packet = $this->commandSubscription->recv();
                foreach ($this->subscriptions['command'] as $subscription)
                {
                        if (substr($packet, 0, strlen($subscription)) === $subscription)
                        {
                                $data = substr($packet, strlen($subscription));
                                $parts = explode(' ', $data);
                                switch ($parts[0])
                                {
                                        case clientCommands::CMD_CHANGE_CHANNEL:
                                                return $this->changeChannel($parts[1]);
                                                break;
                                        case clientCommands::CMD_STOP:
                                                return false;
                                                break;
                                        case clientCommands::CMD_ADD_CHANNEL:
                                                return $this->subscribe('video', $parts[1]);
                                                break;
                                        case clientCommands::CMD_REMOVE_CHANNEL:
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