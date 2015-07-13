<?php

$context = new ZMQContext();

$subscriber = new ZMQSocket($context, ZMQ::SOCKET_SUB);
$subscriber->connect("tcp://localhost:8100");

$subscriber->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, 'mkv');

$hasContainer = false;
$handle = fopen('/var/www/mkv.header', 'r');

do
{
        flock($handle, LOCK_EX | LOCK_NB, $wouldblock);
        //print "checking file lock..." . PHP_EOL;
}
while ($wouldblock);
//print "unlocking" . PHP_EOL;
flock($handle, LOCK_UN);

//print "unlocked" . PHP_EOL;

while (!feof($handle))
{
        print fread($handle, 1024);
}

//print "entering loop" . PHP_EOL;

while (true)
{
        //print "GOT SOMETHING" . PHP_EOL;
        $mkv = substr($subscriber->recv(), 3);
        //var_dump($mkv[0]);
        if ($hasContainer)
        {
                //print "HAVE CONTAINER" . PHP_EOL;
                print substr($mkv, 1);
        }
        elseif ($mkv[0] === '1')
        {
                $hasContainer = true;
                print substr($mkv, 1);
                //print "JUST GOT CONTAINER" . PHP_EOL;
        }
        else
        {
                //print "NO CONTAINER" . PHP_EOL;
        }
}