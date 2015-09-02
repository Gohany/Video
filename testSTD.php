<?php

require_once 'includes.php';

class cmdIn extends stdin
{
        
        protected static $inputs = [
            '-i' => 'iverode',
        ];
        
}

$cmdin = cmdIn::input();
var_dump($cmdin);