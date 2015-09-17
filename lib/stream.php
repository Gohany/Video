<?php

class fileHandle
{

        public static $instance;
        public $bin = '';
        public $position = 0;
        public $removed = 0;

        public static function singleton()
        {
                self::$instance || self::$instance = new fileHandle();
                return self::$instance;
        }

        public static function read($handle, $size)
        {
                if ((self::singleton()->position() + $size) > strlen(self::singleton()->bin))
                {
                        while ((self::singleton()->position() + $size) > strlen(self::singleton()->bin) && !feof($handle))
                        {
                                $bin = fread($handle, 76800);
                                self::singleton()->bin .= $bin;
                        }
                }
                $data = substr(self::singleton()->bin, self::singleton()->position(), $size);
                self::singleton()->position += $size;

                if (self::singleton()->position() > 3000000)
                {

                        if (defined("__DEBUG__") && __DEBUG__)
                        {
                                print "MEM USAGE: " . memory_get_usage() . PHP_EOL;
                                print "BIN LENGTH: " . strlen(self::singleton()->bin) . PHP_EOL;
                        }

                        self::singleton()->bin = substr(self::singleton()->bin, 1500000);
                        self::singleton()->removed += 1500000;

                        if (defined("__DEBUG__") && __DEBUG__)
                        {
                                print "REMOVING.. " . PHP_EOL;
                                print "BIN LENGTH: " . strlen(self::singleton()->bin) . PHP_EOL;
                                print "MEM USAGE: " . memory_get_usage() . PHP_EOL;
                        }
                }

                return $data;
        }

        public static function position()
        {
                return self::singleton()->position - self::singleton()->removed;
        }

        public static function seek($handle, $position)
        {
                self::singleton()->position = $position;
        }

        public static function bin()
        {
                return self::singleton()->bin;
        }

}

class mkvStream
{

        public $zmqPublishPort = zmqPorts::DEFAULT_STREAM_PORT;
        public $zmqContext;
        public $zmqPublisher;
        public $publishString;
        public $headerHandle;
        public $pastHeader = false;
        public $id;
        public $timestart;
        public $previousTimecode;
        public $timeSent;
        
        const RATE_LIMIT = false;

        public function __construct($id, $port, $fileHandle)
        {
                //print "STARTING READER" . PHP_EOL;
                $reader = new EBMLReader($fileHandle);
                $root = new EBMLElementList('root', $reader, 0, '');
                
                $this->id = $id;
                $this->zmqPublishPort = $port;
                $this->zmqContext = new ZMQContext();
                $this->zmqPublisher = $this->zmqContext->getSocket(ZMQ::SOCKET_PUB);
                $this->zmqPublisher->bind(zmqPorts::DEFAULT_STREAM_PROTOCOL . "://*:" . $this->zmqPublishPort);
                $this->headerHandle = fopen('/var/www/mkv.' . $id . '.header', 'w');
                flock($this->headerHandle, LOCK_EX);
                
                $this->publishString = 'mkv.' . $id;
                
                $this->iterateElements($root);
                
        }

        public function iterateElements(&$elements)
        {
                
                foreach ($elements as $element)
                {
                        
                        switch (get_class($element))
                        {
                                case 'EBMLElementList':
                                        
                                        if ($this->pastHeader === false && $element->name() == 'Cluster')
                                        {
                                                $this->pastHeader = true;
                                                flock($this->headerHandle, LOCK_UN);
                                        }
                                        elseif ($this->pastHeader === false)
                                        {
                                                //WRITING CONTAINER
                                                fwrite($this->headerHandle, $element->_head);
                                        }

                                        if ($this->pastHeader === true)
                                        {
                                                //SENDING CONTAINER
                                                $this->zmqPublisher->send($this->publishString . 1 . $element->_head);
                                        }
                                        
                                        $this->iterateElements($element);
                                        break;
                                case 'EBMLElement':

                                        if ($this->pastHeader === true)
                                        {
                                                
                                                if (self::RATE_LIMIT === true && $element->name() == 'Timecode')
                                                {
                                                        
                                                        $timecode = ebmlUtil::ebmlDecodeInt($element->_content->readAll());
                                                        if (empty($this->timestart))
                                                        {
                                                                $microtime = microtime(true);
                                                                $beginningOfHour = mktime(date('H'), 0, 0);
                                                                $this->timestart = (int) round(($microtime - $beginningOfHour) * 1000);
                                                                $content = (int) ($timecode + $this->timestart);
                                                        }
                                                        elseif (isset($this->previousTimecode))
                                                        {
                                                                $content = (int) ($timecode + $this->timestart);
                                                                $videoTimeSegment = $content - $this->previousTimecode;
                                                                //print "VIDEO TIME SEGMENT: " . $videoTimeSegment . PHP_EOL;
                                                                $timeSince = (microtime(true) - $this->timeSent);
                                                                
                                                                //print "TIME SINCE: " . $timeSince . PHP_EOL;
                                                                
                                                                if ($videoTimeSegment > $timeSince)
                                                                {
                                                                        $sleep = ($videoTimeSegment - $timeSince) * 1000;
                                                                        //print "SLEEPING: " . $sleep . PHP_EOL;
                                                                        usleep($sleep);
                                                                }
                                                                
                                                        }
                                                        else
                                                        {
                                                                $content = (int) ($timecode + $this->timestart);
                                                        }
                                                        
                                                        //print "TIMECODE: " . $content . PHP_EOL;
                                                        //print "PREVIOUS TIMECODE: " . $this->previousTimecode . PHP_EOL;
                                                        $timestampValue = pack('N', $content);
                                                        $timecode = ebmlUtil::ebmlEncodeElement('Timecode', $timestampValue);
                                                        $this->zmqPublisher->send($this->publishString . 0 . $timecode);
                                                        $this->previousTimecode = $content;
                                                        $this->timeSent = microtime(true);
                                                }
                                                else
                                                {
                                                        
//                                                        if ($element->name() == 'Timecode')
//                                                        {
//                                                                $prefix = str_pad(ebmlUtil::ebmlDecodeInt($element->_content->readAll()), 7, '0', STR_PAD_LEFT);
//                                                        }
//                                                        else
//                                                        {
//                                                                $prefix = '0000000';
//                                                        }
                                                        
                                                        //$this->zmqPublisher->send($this->publishString . $prefix . 0 . $element->_head . $element->_content->readAll());
                                                        $this->zmqPublisher->send($this->publishString . 0 . $element->_head . $element->_content->readAll());
                                                }
                                        }
                                        else
                                        {
                                                fwrite($this->headerHandle, $element->_head . $element->_content->readAll());
                                        }
                                        unset($element);
                                        break;
                        }
                        unset($element);
                }
                unset($elements);
        }

}
