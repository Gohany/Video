<?php

class settings
{
        
        // SEND TO REMOTE
        //rsync -avz /var/www/rsync root@192.168.2.13:/var/www
        // GET FROM REMOVE
        //rsync -avzh root@192.168.2.6:/var/www/rsync /var/www/rsync
        public $settings;
        
        const ROOT_DIR = '/var/www';
        const SETTINGS_DIR = '/var/www/settings';
        const SETTINGS_EXT = '.hrs';
        
        public function __construct()
        {
                foreach (glob(self::SETTINGS_DIR . "/*" . self::SETTINGS_EXT) as $filename)
                {
                        print "FILENAME: " . $filename . PHP_EOL;
                        $this->settings[basename($filename, self::SETTINGS_EXT)] = json_decode(file_get_contents($filename));
                }
        }
        
        public function pushSettings()
        {
                
                // write to setttings dir
                // foreach nodes
                // rsync
                
                if (!empty($this->settings))
                {
                        
                        foreach ($this->settings as $name => $array)
                        {
                                $this->writeArrayToFile($name, $array);
                        }
                        
                        if (!($systems = systems::byLatest()))
                        {
                                throw new Exception('No systems to push settings to');
                        }
                        
                        foreach ($systems->systems as $system)
                        {
                                $rsync = exec("rsync -az " . self::SETTINGS_DIR . ' ' . self::RSYNC_USER . '@' . $system->ip . ':' . self::ROOT_DIR);
                        }
                        
                }
                else
                {
                        print "SETTINGS WERE EMPTY!" . PHP_EOL;
                }
                
        }
        
        public function pullSettings()
        {
                
                // foreach nodes
                // rsync
                
                if (!($systems = systems::byLatest()))
                {
                        throw new Exception('No systems to get settings from');
                }
                
                $system = current($systems);
                // user first system?
                
                $rsync = exec("rsync -azh " . self::RSYNC_USER . '@' . $system->ip . ':' . self::SETTINGS_DIR . ' ' . self::SETTINGS_DIR);
                
        }
        
        public function writeArrayToFile($name, $array)
        {
                if (is_array($array))
                {
                        if ($fp = fopen(self::SETTINGS_DIR . '/' . $name . self::SETTINGS_EXT, 'w+'))
                        {
                                fwrite($fp, json_encode($array));
                                return true;
                                fclose($fp);
                        }
                        fclose($fp);
                }
                return false;
        }
        
}