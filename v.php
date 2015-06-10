<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit','1024M');

// register input stream
//      input format
//      output format
//      metadata
// register file
//      format
// generate unique url
// spawn php retrieval 
// create ffmpeg string

// -c copy == use default shits
// -vf scale=320:-1 || -1:x to resize [after input && before output]

header("Content-Type: video/mp4");
$handle = popen('ffmpeg -re -loglevel panic -hide_banner -nostats -i /var/www/h264.mp4 -bsf:v h264_mp4toannexb -map_metadata -1 -c copy -f mpegts pipe:1', 'r');
$fh = fopen('/var/www/video.mp4', 'w+');

$context = new ZMQContext();
$publisher = $context->getSocket(ZMQ::SOCKET_PUB);
$publisher->bind("tcp://*:5556");
$pub = 'sw ';

$counter = 0;
while(!feof($handle))
{
        $contents = fread($handle, 66560);
        //print $contents;
        $publisher->send($pub . $contents);
//        if (connection_aborted())
//        {
//                pclose($handle);
//                die;
//        }
//        $counter++;
}
pclose($handle);