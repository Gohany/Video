<?php

//require_once '../session.php';
require_once '/var/www/lib/clientCommands.php';
require_once '/var/www/lib/zmqPorts.php';

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
                //$this->session = !empty($_GET['sid']) ? new session($_GET['sid']) : false;
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
                $this->videoData->__destruct();
                exit;
        }

}

class clientVideoData
{

        public $containerSent = false;
        public $switch = false;
        public $context;
        public $videoSubscription;
        public $commandSubscription;
        public $syncService;
        public $unsubQueue;
        public $subQueue;
        public $id;
        public $headerString;
        public $subscriptions = [
            'video' => [],
            'command' => [],
        ];
        public $unsubscriptions = [
            'video' => [],
            'command' => [],
        ];
        
        const VIDEO_PREFIX = 'mkv.';
        const VIDEO_HEADERS_DIR = '/var/www/';

        public function __construct($id = 1)
        {

                $this->id = $id;
                
                $this->headerString = self::VIDEO_HEADERS_DIR . self::VIDEO_PREFIX . $this->id . '.header';
                $this->header();
                
                $this->context = new ZMQContext;
                
                $this->videoSubscription = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                //$this->videoSubscription->identity = sprintf("%04X", rand(0, 0x10000));
                //$this->videoSubscription->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->identity);
                $this->subscribe('video', 'mkv.' . $this->id);
                $this->videoSubscription->connect(zmqPorts::PROXY_PORT_PROTOCOL . "://localhost:" . zmqPorts::PROXY_PORT);
                //$this->videoSubscription->send(1);
                //$this->videoSubscription->send('mkv.' . $this->id);

                $this->commandSubscription = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->commandSubscription->connect(zmqPorts::CLIENT_VLISTEN_PROTOCOL . "://localhost:" . zmqPorts::CLIENT_VLISTEN_INSTRUCTION);
                $this->subscribe('command', 'all');

                $this->poll = new ZMQPoll();
                $this->poll->add($this->videoSubscription, ZMQ::POLL_IN);
                $this->poll->add($this->commandSubscription, ZMQ::POLL_IN);
                
                
//                $this->syncService = $this->context->getSocket(ZMQ::SOCKET_REQ);
//                $this->syncService->connect("tcp://localhost:" . self::ZMQ_SYNC_PORT);
//                $this->syncService->send('1mkv.' . $this->id);
//                
//                $wait = $this->syncService->recv();
//                $this->poll->add($this->syncService, ZMQ::POLL_OUT);
                
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

        public function run()
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
                                        case clientCmd::CMD_CHANGE_CHANNEL:
                                                return $this->changeInput($parts[1]);
                                                break;
                                        case clientCmd::CMD_STOP:
                                                return false;
                                                break;
                                        case clientCmd::CMD_ADD_CHANNEL:
                                                return $this->subscribe('video', $parts[1]);
                                                break;
                                        case clientCmd::CMD_REMOVE_CHANNEL:
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
                                        if ($this->switch === true && !empty($this->subQueue['video']))
                                        {
                                                return $this->changeChannel($this->subQueue['video']);
                                        }
                                        else
                                        {
                                                print substr($data, 1);
                                                $this->containerSent = true;
                                        }
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
