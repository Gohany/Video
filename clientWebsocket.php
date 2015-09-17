#!/usr/bin/env php
<?php

// INTERNAL NETWORK.... LIKE BEFORE
// fuckin EVERY TV THAT HAS THE POSSIBILITY OF PLAYING THE VIDEO NEEDS TO START LISTENING BEFORE THE BROADCAST. DO THIS DYNAMICALLY.
// they will all catch the broadcast.

require_once('includes.php');

$echo = new clientWS("0.0.0.0", "9000");
try
{
        $echo->run();
}
catch (Exception $e)
{
        $echo->stdout($e->getMessage());
}