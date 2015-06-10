<?php

$context = new ZMQContext();

$subscriber = new ZMQSocket($context, ZMQ::SOCKET_SUB);
$subscriber->connect("tcp://localhost:5556");

$subscriber->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, 'sw');
while (true)
{
        print substr($subscriber->recv(), 3);
}