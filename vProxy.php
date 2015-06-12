<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit','1024M');

$context = new ZMQContext();

//  This is where the weather server sits
$frontend = new ZMQSocket($context, ZMQ::SOCKET_SUB);
$frontend->connect("tcp://127.0.0.1:5556");

//  This is our public endpoint for subscribers
$backend = new ZMQSocket($context, ZMQ::SOCKET_PUB);
$backend->bind("tcp://*:8100");

//  Subscribe on everything
$frontend->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");

//  Shunt messages out to our own subscribers
while (true) {
    while (true) {
        //  Process all parts of the message
        $message = $frontend->recv();
        $more = $frontend->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
        $backend->send($message, $more ? ZMQ::SOCKOPT_SNDMORE : 0);
        if (!$more) {
            break; // Last message part
        }
    }
}