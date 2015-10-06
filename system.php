<?php

require_once 'includes.php';

class stdsystem extends stdin
{
        public static $inputs = [
            '-a' => 'action',
            '-h' => 'macAddress',
            '-n' => 'number',
        ];
}

try
{
        
        $system = new system;
        $system->run();
        
//        $result = false;
//        $stdin = stdsystem::input();
//        if (!empty($stdin->action))
//        {
//                switch ($stdin->action)
//                {
//                        case 'assign':
//                                if (!empty($stdin->number) && !empty($stdin->macAddress))
//                                {
//                                        $result = system::assignSystemNumber($stdin->number, $stdin->macAddress);
//                                }
//                                break;
//                }
//        }
//        
//        if ($result)
//        {
//                print "SUCCESS" . PHP_EOL;
//        }
//        else
//        {
//                print "FAILURE" . PHP_EOL;
//        }
        
        // 1. set system number identifiers
        // 2. set configs
        // 3. 
        
}
catch (Exception $e)
{
        print "Exception: " . $e->getMessage() . PHP_EOL;
        var_dump($e);
}