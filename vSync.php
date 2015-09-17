<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

require_once 'includes.php';

$proxy = new vSync();
$proxy->run();