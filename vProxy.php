<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '1024M');

$proxy = new zmqProxy();
$proxy->registerBackend('5556');
$proxy->run();

class zmqProxy
{

        const ZMQ_FRONTEND_PORT = '8100';
        const ZMQ_INSTRUCTION_PORT = '6300';

        public $context;
        public $frontend;
        public $backend;
        public $backends;
        public $poll;
        public $instructionService;

        public function __construct()
        {
                $this->backend = new ZMQSocket($this->context, ZMQ::SOCKET_SUB);
                $this->context = new ZMQContext();
                $this->frontend = new ZMQSocket($this->context, ZMQ::SOCKET_PUB);
                $this->frontend->bind("tcp://*:" . self::ZMQ_FRONTEND_PORT);

                $frontend->setSockOpt(ZMQ::SOCKOPT_SUBSCRIBE, "");


                $this->instructionService = new ZMQSocket($context, ZMQ::SOCKET_ROUTER);
                $this->instructionService->bind("tcp://*:" . self::ZMQ_INSTRUCTION_PORT);

                $this->poll = new ZMQPoll();
                $this->poll->add($this->backend, ZMQ::POLL_IN);
                $this->poll->add($this->instructionService, ZMQ::POLL_IN);
        }

        public function registerBackend($port, $ip = '127.0.0.1', $protocol = 'tcp')
        {
                $networkString = $protocol . '://' . $ip . ':' . $port;
                $this->backend->connect($networkString);
                $this->backends[] = $networkString;
        }

        public function run()
        {
                $message = '';
                $readable = $writeable = array();
                while (true)
                {
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
                                                        $message = $this->backend->recv();
                                                        //  Multipart detection
                                                        $more = $this->backend->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
                                                        $this->frontend->send($message, $more ? ZMQ::SOCKOPT_SNDMORE : 0);
                                                        if (!$more)
                                                        {
                                                                break; // Last message part
                                                        }
                                                }
                                                elseif ($socket === $this->instructionService)
                                                {
                                                        $message .= $this->instructionService->recv();
                                                        //  Multipart detection
                                                        $more = $socket->getSockOpt(ZMQ::SOCKOPT_RCVMORE);
                                                        
                                                        $frontend->send($message, $more ? ZMQ::MODE_SNDMORE : null);
                                                        if (!$more)
                                                        {
                                                                break; //  Last message part
                                                        }
                                                }
                                        }
                                }
                        }
                }
        }

}
