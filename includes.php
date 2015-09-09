<?php

//foreach (glob('/var/www/*.php') as $phpFile)
//{
//        require_once $phpFile;
//}

define('__BASE_DIR__', '/var/www/');

require_once __BASE_DIR__ . 'clientCommands.php';
require_once __BASE_DIR__ . 'dataStore.php';
require_once __BASE_DIR__ . 'zmsg.php';
require_once __BASE_DIR__ . 'db.php';
require_once __BASE_DIR__ . 'feeds.php';
require_once __BASE_DIR__ . 'session.php';
require_once __BASE_DIR__ . 'stdin.php';
require_once __BASE_DIR__ . 'zmqPorts.php';
require_once __BASE_DIR__ . 'process.php';
require_once __BASE_DIR__ . 'requests.php';