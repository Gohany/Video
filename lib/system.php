<?php

class system
{
        
        // 1. set system number identifiers
        // 2. set configs
        // 3. 
        
        public $configs;
        
        const ROOT_DIR = '/var/www';
        const CONFIGS_DIR = '/var/www/configs';
        
        public static function assignSystemNumber($number, $macAddress)
        {
                
                // MAKE A CALL TO THE CONTROLLER? DO IT FROM CLIENT? 
                // MAKE A THING THAT ACTUALLY STARTS TEH CONTROLLER AND VSYNC AND DISCOVERY?? probably..
                // talk to THAT
                $nodes = nodes::byActive();
                var_dump($nodes);
                foreach ($nodes->nodes as $node)
                {
                        print "RESULT: " . PHP_EOL;
                        var_dump($node->setSystemNumber($number, $macAddress));
                }
                
//                if ($activeNode = node::fromMacAddress($macAddress))
//                {
//                        if ($activeNode->system_number == $number)
//                        {
//                                return true;
//                        }
//
//                        if ($node = node::fromSystemNumber($number))
//                        {
//                                $node->setSystemNumber(NULL);
//                        }
//
//                        return $activeNode->setSystemNumber($number);
//                }
                return true;
        }        
        
}