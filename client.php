<?php

require_once 'zmsg.php';

$client = new client;
$client->request();

class client
{

        public $context;
        public $client;
        public $identity;
        public $poll;
        
        const ZMQ_SERVICE_PORT = 6200;

        public function __construct()
        {
                $this->context = new ZMQContext();
                $this->client = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);

                //  Generate printable identity for the client
                $this->identity = sprintf("%04X", rand(0, 0x10000));
                $this->client->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->identity);
                $this->client->connect("tcp://localhost:" . self::ZMQ_SERVICE_PORT);


                $this->poll = new ZMQPoll();
                $this->poll->add($this->client, ZMQ::POLL_IN);
        }

        public function request()
        {

                $zmsg = new Zmsg($this->client);
                $zmsg->body_set('i want dis thing');
                $zmsg->send();

                $read = $write = array();
                while (true)
                {
                        $events = $this->poll->poll($read, $write, 1000);
                        $zmsg = new Zmsg($this->client);
                        if ($events)
                        {
                                $zmsg->recv();
                                printf("%s: %s%s", $this->identity, $zmsg->body(), PHP_EOL);
                                break;
                        }
                }
        }

}
