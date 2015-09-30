<?php

class utilities
{
        
        public static function ifconfig($range = '192.168.2')
        {
                $process = popen("ifconfig | grep -B 1 'inet addr:" . trim($range, "*")."'", 'r');
                print "EXECUTING: " . "ifconfig | grep -B 1 'inet addr:" . trim($range, "*")."'" . PHP_EOL;
                $line = trim(fread($process, 1024));
                print $line . PHP_EOL;
                pclose($process);
                $pattern = '/\s{2,}/';
                $line = preg_replace($pattern, ' ', $line);
                print $line;
                print PHP_EOL;
                preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $line, $matches);
                // returns ip, bcast, subnet;
                return $matches;
        }
        
}

utilities::ifconfig();