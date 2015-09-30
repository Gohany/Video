<?php

class ifconfig
{
        
        const CONFIG_DIR = '/var/www/configs/ifconfig';
        const LOCAL_RANGE_FILE = '/localRange';
        const CONFIG_EXT = '.config';
        const DEFAULT_LOCAL_RANGE = "192.168";
        
        public static $instance;
        public $interface;
        public $interfaceType;
        public $macAddress;
        public $ip;
        public $broadcast;
        public $subnet;

        public static function singleton()
        {
                self::$instance || self::$instance = new ifconfig();
                return self::$instance;
        }
        
        public function __construct()
        {
                
                if (!($contents = file_get_contents(self::CONFIG_DIR . self::LOCAL_RANGE_FILE . self::CONFIG_EXT)))
                {
                        $contents = self::DEFAULT_LOCAL_RANGE;
                }
                $range = trim($contents, '"');
                
                $process = popen("ifconfig | grep -B 1 'inet addr:" . trim($range, "*")."'", 'r');
                $line = trim(fread($process, 1024));
                pclose($process);
                
                $pattern = '/\s{2,}/';
                $line = preg_replace($pattern, ' ', $line);
                
                $parse = explode(' ', $line);
                for ($i=0, $c=count($parse); $i<$c; $i++)
                {
                        
                        if ($i == 0)
                        {
                                $this->interface = $parse[$i];
                                continue;
                        }
                        
                        switch ($parse[$i])
                        {
                                case 'Link':
                                        $this->interfaceType = substr(strrchr($parse[$i+1], ':'), 1);
                                        break;
                                case 'HWaddr':
                                        $this->macAddress = $parse[$i+1];
                                        break;
                                case 'inet':
                                        $this->ip = substr(strrchr($parse[$i+1], ':'), 1);
                                        $this->broadcast = substr(strrchr($parse[$i+2], ':'), 1);
                                        $this->subnet = substr(strrchr($parse[$i+3], ':'), 1);
                                        break;
                        }
                }
                
        }
        
        public static function info()
        {
                return self::singleton();
        }
        
        public static function ip()
        {
                return self::singleton()->ip;
        }
        
        public static function subnet()
        {
                return self::singleton()->subnet;
        }
        
        public static function macAddress()
        {
                return self::singleton()->macAddress;
        }
        
        
        
}