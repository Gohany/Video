<?php

require_once 'includes.php';
$client = new client;

$stdin = stdin::input();

try
{
        if (!isset($stdin->sid) && empty($stdin->sid))
        {
                $id = $stdin->id;
                $client->request($stdin->cmd, $stdin->id);
        }
        else
        {
                $client->command('all', clientCmd::CMD_CHANGE_CHANNEL . ' mkv.' . $stdin->sid);
        }
}
catch (Exception $e)
{
        print $e->getMessage();
        die;
}

class client
{

        public $context;
        public $client;
        public $command;
        public $identity;
        public $poll;
        public $session;
        
        const ZMQ_COMMAND_PORT = 8101;

        public function __construct()
        {
                $this->context = new ZMQContext();
                $this->client = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);

                //  Generate printable identity for the client
                $this->identity = 'cl' . getmypid();
                $this->client->setSockOpt(ZMQ::SOCKOPT_IDENTITY, $this->identity);
                $this->client->connect(zmqPorts::CLIENT_CONTROLLER_PROTOCOL . "://localhost:" . zmqPorts::CLIENT_CONTROLLER_INSTRUCTION);


                $this->poll = new ZMQPoll();
                $this->poll->add($this->client, ZMQ::POLL_IN);
                
                if (!empty($_GET['sid']))
                {
                        $this->session = new session($_GET['sid']);
                        $this->command = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                        $this->command->bind(zmqPorts::CLIENT_VLISTEN_PROTOCOL . "://*:" . zmqPorts::CLIENT_VLISTEN_INSTRUCTION);
                }
                
        }
        
        public function command($who, $what)
        {
                $this->command = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                $this->command->bind(zmqPorts::CLIENT_VLISTEN_PROTOCOL . "://*:" . zmqPorts::CLIENT_VLISTEN_INSTRUCTION);
//                while (true)
//                {
                        sleep(2);
                        $this->command->send($who . $what);
//                }
        }

        public function request($cmd, $id = null)
        {
                
                if (!defined('clientCmd::' . $cmd))
                {
                     throw new Exception("Undefined command");
                }
                
                $zmsg = new Zmsg($this->client);
                $zmsg->set(constant('clientCmd::' . $cmd), $id, 'cmd');
                $zmsg->send();
                
                $read = $write = array();
                while (true)
                {
                        print "whiling.. " . PHP_EOL;
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
