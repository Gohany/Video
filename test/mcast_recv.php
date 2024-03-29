<?php

error_reporting(E_ALL | E_STRICT);

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
socket_set_option($socket, SOL_SOCKET, SO_BROADCAST, 1);
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1,"usec"=>0));

$time = time();
//$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); 

//socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);

function sendHello($sock, $port)
        
{
        $broadcast_string = 'hello world!3';
        print "SENDING TO PORT: " . $port . PHP_EOL;
        socket_sendto($sock, $broadcast_string, strlen($broadcast_string), 0, '255.255.255.255', $port);
}

$connected = [];

//$mcast_group = [
//    'group' => '192.168.2.6',
//    'interface' => 'eth0',
    //'source' => '192.168.2.6',
//];

//socket_set_option($socket, SOL_SOCKET);//, MCAST_JOIN_SOURCE_GROUP, $mcast_group);
$binded = socket_bind($socket, '0.0.0.0', 4444);
//$binded = socket_bind($socket, '127.0.0.1', 4444);

$from = '';
$port = 4444;

sendHello($socket, $port);

while (true)
{
        //$port = 4444;
        print "RECEIVING.. " . PHP_EOL;
        print time() - $time . PHP_EOL;
        
        socket_recvfrom($socket, $buf, 13, MSG_WAITALL, $from, $port);
        //echo "Received $buf from remote address $from and remote port $port" . PHP_EOL;
        $connected[$from] = time();
        sendHello($socket, $port);
        usleep(500000);
        
        
        if (($errorcode = socket_last_error($socket)) || ($errorcode = socket_last_error($socket)))
        {
                $errormsg = socket_strerror($errorcode);
                print "ERR0R: " . $errormsg . PHP_EOL;
        }
        
        foreach ($connected as $who => $time)
        {
                print $who . " connected last: " . (time() - $time) . " seconds ago" . PHP_EOL;
        }
        
}

