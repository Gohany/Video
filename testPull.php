<?php

$context = new ZMQContext();
$client = new ZMQSocket($context, ZMQ::SOCKET_DEALER);

//  Generate printable identity for the client
$identity = sprintf("%04X", rand(0, 0x10000));
$client->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $identity);
$client->connect("tcp://localhost:8100");

//$read = $write = array();
//$poll = new ZMQPoll();
//$poll->add($client, ZMQ::POLL_IN);
$client->send('hi');

while (true)
{
        $client->send('hi');
        print "Trying to recv.. " . PHP_EOL;
        print $client->recv();
        
//        $events = $poll->poll($read, $write, 1000);
//        if ($events)
//        {
//                print "HERE.." . PHP_EOL;
//                foreach ($read as $socket)
//                {
//                        print $socket->recv();
//                }
//        }
        
}