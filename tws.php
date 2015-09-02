#!/usr/bin/env php
<?php

// INTERNAL NETWORK.... LIKE BEFORE
// fuckin EVERY TV THAT HAS THE POSSIBILITY OF PLAYING THE VIDEO NEEDS TO START LISTENING BEFORE THE BROADCAST. DO THIS DYNAMICALLY.
// they will all catch the broadcast.

require_once('websockets.php');
require_once('includes.php');

class tWS extends WebSocketServer
{
        
        public $containerSent = false;
        public $switch = false;
        public $context;
        public $videoSubscription;
        public $commandSubscription;
        public $unsubQueue;
        public $subQueue;
        public $id;
        public $headerString;
        public $header = '';
        public $subscriptions = [
            'video' => [],
            'command' => [],
        ];
        public $unsubscriptions = [
            'video' => [],
            'command' => [],
        ];

        const PROXY_PORT = 8100;
        const COMMAND_PORT = 8101;
        const VIDEO_PREFIX = 'mkv.';
        const VIDEO_HEADERS_DIR = '/var/www/';
        
        public function __construct($id = 1, $addr, $port, $bufferLength = 2048)
        {

                $this->id = $id;
                
                $this->headerString = self::VIDEO_HEADERS_DIR . self::VIDEO_PREFIX . $this->id . '.header';
                $this->header();
                
                $this->context = new ZMQContext;
                $this->videoSubscription = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->subscribe('video', 'mkv.' . $this->id);
                $this->videoSubscription->connect("tcp://localhost:" . self::PROXY_PORT);
                

                $this->commandSubscription = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->commandSubscription->connect("tcp://localhost:" . self::COMMAND_PORT);
                $this->subscribe('command', 'all');

                $this->poll = new ZMQPoll();
                $this->poll->add($this->videoSubscription, ZMQ::POLL_IN);
                $this->poll->add($this->commandSubscription, ZMQ::POLL_IN);
                
                parent::__construct($addr, $port, $bufferLength);
                
        }
        
        public function subscribe($socket, $subscription)
        {
                if (!empty($this->{$socket . 'Subscription'}))
                {

                        try
                        {
                                $this->{$socket . 'Subscription'}->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, $subscription);
                        }
                        catch (ZMQSocketException $ex)
                        {
                                var_dump($ex);
                                throw new Exception('Failed to subscribe to ' . $subscription . ' on socket ' . $socket);
                                return false;
                        }

                        $this->subscriptions[$socket][] = $subscription;
                        return true;
                }
                return false;
        }

        public function unsubscribe($socket, $subscription)
        {
                if (!empty($this->{$socket . 'Subscription'}) && in_array($subscription, $this->subscriptions[$socket]))
                {
                        try
                        {
                                $this->{$socket . 'Subscription'}->setSockOpt(ZMQ::SOCKOPT_UNSUBSCRIBE, $subscription);
                        }
                        catch (ZMQSocketException $ex)
                        {
                                throw new Exception('Failed to unsubscribe to ' . $subscription . ' on socket ' . $socket);
                                return false;
                        }
                        $key = array_search($subscription, $this->subscriptions[$socket]);
                        unset($this->subscriptions[$socket][$key]);
                        return true;
                }
                return false;
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
                                                return $this->changeInput($parts[1]);
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
                $this->switch = false;
                return $this->subscribe('video', $channel);
        }

        public function changeInput($subscription)
        {
                $this->subQueue['video'] = $subscription;
                $this->switch = true;
                $this->containerSent = false;
                return true;
        }
        
        protected function tick()
        {
                $readable = $writeable = array();
                $events = $this->poll->poll($readable, $writeable, 100);
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
                                        //print substr($data, 1);
                                        $this->sendAll(substr($data, 1));
                                }
                                elseif ($data[0] === '1')
                                {
                                        if ($this->switch === true && !empty($this->subQueue['video']))
                                        {
                                                return $this->changeChannel($this->subQueue['video']);
                                        }
                                        else
                                        {
                                                $this->sendAll(substr($data, 1));
                                                $this->containerSent = true;
                                        }
                                }
                        }
                }
        }
        
        protected function process($user, $message)
        {
                
                $explode = explode('|', $message);
                $video = $explode[0];
                $time = $explode[1];
                
                $this->times[$video][$user->id] = $time;
                
                var_dump($this->times);
                $this->send($user, 'ack');
        }
        
        protected function closed($user)
        {
                print "PROCESSING CLOSED FOR ".$user->id . PHP_EOL;
                foreach ($this->times as $video => $array)
                {
                        if (isset($array[$user->id]))
                        {
                                unset($this->times[$video][$user->id]);
                        }
                }
        }
        
        protected function connected($user)
        {
                $this->send($user, $this->header);
        }
        
        public function sendAll($data)
        {
                foreach ($this->users as $user)
                {
                        $this->send($user, $data);
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
                        $this->header .= fread($handle, 1024);
                }
        }
        
}

$echo = new tWS(1, "0.0.0.0", "9000");
try
{
        $echo->run();
}
catch (Exception $e)
{
        $echo->stdout($e->getMessage());
}