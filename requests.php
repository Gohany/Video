<?php

require_once 'includes.php';

class clientRequest extends request
{
        
        const ADDRESS_PREFIX = 'cr';
        
        public function __construct(Zmsg $zmsg, vController $controller)
        {
                var_dump($zmsg);
                parent::__construct($zmsg);
                
                switch ($this->cmd)
                {
                        // add another source to current viewing
                        case clientCmd::ADD_SOURCE:
                        // make new source the only source
                        case clientCmd::NEW_SINGLE_SOURCE:
                                $this->startPublisher($controller);
                                break;
                        // remove a viewed source
                        case clientCmd::REMOVE_SOURCE:
                                
                                break;
                        // make current source the only source
                        case clientCmd::SINGLE_SOURCE:
                                
                                break;
                        // make blank
                        case clientCmd::REMOVE_ALL:
                                
                                break;
                }
        }

        public function startPublisher(vController $controller)
        {
                
                if (!is_numeric($this->id) || !($controller->feeds[$this->id] = feed::fromID($this->id)))
                {
                        $this->zmsg->body_set('failure')->send();
                        return false;
                }
                
                $port = array_search(1, $controller->ports);
                $controller->ports[$port] = 0;
                $controller->feeds[$this->id]->port = $port;
                $controller->feeds[$this->id]->update();
                
                $this->zmsg->push($port);
                $this->zmsg->wrap(self::ADDRESS_PREFIX . getmypid());
                
                $this->zmsg->set_socket($controller->sockets['vSync'])->send();
                
        }

}

class vSyncRequest extends request
{
        
        public $response;
        
        public function __construct(Zmsg $zmsg, vController $controller)
        {
                
                parent::__construct($zmsg);
                $this->response = $zmsg->pop();
                
                
        }
        
}

class request
{
        
        public $zmsg;
        public $cmd;
        public $id;
        public $feed;
        public $address;
        public $action;

        public function __construct(Zmsg $zmsg)
        {
                $this->zmsg = $zmsg;
                $this->address = $zmsg->unwrap();
                $this->action = $zmsg->body();
                list($this->cmd, $this->id) = $zmsg->extract();
        }
        
}