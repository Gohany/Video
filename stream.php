<?php

require_once 'ebml.php';
require_once 'ebmlElements.php';

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

                if (self::singleton()->position() > 1500000)
                {

                        if (defined("__DEBUG__") && __DEBUG__)
                        {
                                print "MEM USAGE: " . memory_get_usage() . PHP_EOL;
                                print "BIN LENGTH: " . strlen(self::singleton()->bin) . PHP_EOL;
                        }

                        self::singleton()->bin = substr(self::singleton()->bin, 750000);
                        self::singleton()->removed += 750000;

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

        public $zmqPublishPort = '5556';
        public $zmqContext;
        public $zmqPublisher;
        public $publishString;
        public $headerHandle;
        public $pastHeader = false;
        public $id;

        public function __construct($id, $port, $fileHandle)
        {
                
                $reader = new EBMLReader($fileHandle);
                $root = new EBMLElementList('root', $reader, 0, '');
                
                $this->id = $id;
                $this->zmqPublishPort = $port;
                $this->zmqContext = new ZMQContext();
                $this->zmqPublisher = $this->zmqContext->getSocket(ZMQ::SOCKET_PUB);
                $this->zmqPublisher->bind("tcp://*:" . $this->zmqPublishPort);
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
                                                $this->zmqPublisher->send($this->publishString . 0 . $element->_head . $element->_content->readAll());
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
