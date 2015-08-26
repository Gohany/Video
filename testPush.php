<?php

$context = new ZMQContext();
$backend = new ZMQSocket($context, ZMQ::SOCKET_SUB);
$backend->connect('tcp://localhost:6400');
$backend->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");

$frontend = new ZMQSocket($context, ZMQ::SOCKET_ROUTER);
$frontend->bind("tcp://*:8100");

$poll = new ZMQPoll();
$poll->add($backend, ZMQ::POLL_IN);
$poll->add($frontend, ZMQ::POLL_IN);
$poll->add($frontend, ZMQ::POLL_OUT);


while (true)
{
        $readable = $writeable = array();
        $events = $poll->poll($readable, $writeable);
        if ($events > 0)
        {

                foreach ($readable as $socket)
                {
                        if ($socket === $backend)
                        {
                                //  Process all parts of the message
                                $msg = $backend->recv();
                                //  Multipart detection
                                foreach ($writeable as $socket)
                                {
                                        if ($socket === $frontend)
                                        {
                                                print "SENDING.. " . PHP_EOL;
                                                //$more = $backend->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
                                                $frontend->send($msg, 0);
//                                                if (!$more)
//                                                {
//                                                        break; // Last message part
//                                                }
                                        }
                                }
                        }
                        elseif ($socket === $frontend)
                        {
//                                print "SENDING CACHE!" . PHP_EOL;
//                                $message = $frontend->recv();
//                                if (isset($cache[1]))
//                                {
//                                        $frontend->send($cache[1]);
//                                }
                        }
                }
        }
}