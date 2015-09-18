<?php

error_reporting(E_ALL | E_STRICT);

$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); 
socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);

function sendHello($sock, $port)
{
        $broadcast_string = 'hello world!';
        print "SENDING TO PORT: " . $port . PHP_EOL;
        socket_sendto($sock, $broadcast_string, strlen($broadcast_string), 0, '255.255.255.255', $port);
}

//$mcast_group = [
//    'group' => '192.168.2.6',
//    'interface' => 'eth0',
    //'source' => '192.168.2.6',
//];

//socket_set_option($socket, SOL_SOCKET);//, MCAST_JOIN_SOURCE_GROUP, $mcast_group);
$binded = socket_bind($socket, '0.0.0.0', 4444);

$from = '';
$port = 4444;

sendHello($sock, $port);

while (true)
{
        $port = 4444;
        print "RECEIVING.. " . PHP_EOL;
        socket_recvfrom($socket, $buf, 12, MSG_WAITALL, $from, $port);
        echo "Received $buf from remote address $from and remote port $port" . PHP_EOL;
        sendHello($sock, $port);
        sleep(1);
        
        if (($errorcode = socket_last_error($socket)) || ($errorcode = socket_last_error($sock)))
        {
                $errormsg = socket_strerror($errorcode);
                print "ERR0R: " . $errorcode . PHP_EOL;
        }
        
}

