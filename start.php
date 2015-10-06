<?php

require_once 'includes.php';

class stdstart extends stdin
{
        public static $inputs = [
            '-f' => 'file',
        ];
}

$stdin = stdstart::input();

if (!empty($stdin->file))
{
        $tryForMicroseconds = 1000000;
        $sleepForMicroseconds = 200000;
        
        $i = 0;
        while (Process::isRunning('php /var/www/' . $stdin->file))
        {
                
                if (($i * $sleepForMicroseconds) >= $tryForMicroseconds)
                {
                        exit;
                        break;
                }
                
                usleep($sleepForMicroseconds);
                $i++;
        }
        
        $process = new Process(PHP_BINDIR . '/php /var/www/' . $stdin->file);
        
}