<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

require_once 'zmsg.php';

$proxy = new zmqProxy();
//$proxy->registerBackend('5556');
$proxy->run();

class zmqProxy
{

        const ZMQ_FRONTEND_PORT = 8100;
        const ZMQ_BACKEND_PORT = 5556;
        const ZMQ_INSTRUCTION_PORT = 'backend';

        public $context;
        public $frontend;
        public $backend;
        public $backends;
        public $poll;
        public $instructionService;

        public function __construct()
        {
                $this->context = new ZMQContext();

                $this->backend = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->registerBackend(self::ZMQ_BACKEND_PORT);

                $this->frontend = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                $this->frontend->bind("tcp://*:" . self::ZMQ_FRONTEND_PORT);

                $this->backend->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");

                $this->instructionService = new ZMQSocket($this->context, ZMQ::SOCKET_DEALER);
                $this->instructionService->connect("ipc://" . self::ZMQ_INSTRUCTION_PORT);

                $this->poll = new ZMQPoll();
                $this->poll->add($this->backend, ZMQ::POLL_IN);
                $this->poll->add($this->instructionService, ZMQ::POLL_IN);

                $this->zmsg = new Zmsg($this->instructionService);
        }

        public function registerBackend($port, $ip = '127.0.0.1', $protocol = 'tcp')
        {
                $networkString = $protocol . '://' . $ip . ':' . $port;
                if ($this->backend->connect($networkString))
                {
                        $this->backends[] = $networkString;
                        print_r($this->backends);
                        return true;
                }
                return false;
        }

        public function run()
        {
                $message = [
                    'backend' => '',
                    'instructions' => '',
                ];
                $readable = $writeable = array();
                while (true)
                {

                        $events = $this->poll->poll($readable, $writeable);
                        if ($events > 0)
                        {
                                
                                foreach ($readable as $socket)
                                {
                                        if ($socket === $this->backend)
                                        {
                                                //  Process all parts of the message
                                                $message['backend'] .= $this->backend->recv();
                                                //  Multipart detection
                                                $more = $this->backend->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
                                                $this->frontend->send($message, $more ? ZMQ::SOCKOPT_SNDMORE : 0);
                                                print "SENDING STUFF!" . PHP_EOL;
                                                if (!$more)
                                                {
                                                        $message['backend'] = '';
                                                        break; // Last message part
                                                }
                                        }
                                        elseif ($socket === $this->instructionService)
                                        {
                                                print "NEW PORT REQUEST" . PHP_EOL;
                                                $this->zmsg->recv();
                                                print $this->zmsg->body() . PHP_EOL;
                                                // do stuff
                                                $this->registerBackend($this->zmsg->body());
                                                $this->zmsg->body_set('success');
                                                $this->zmsg->send(true);
                                        }
                                }
                        }
                }
        }

}
