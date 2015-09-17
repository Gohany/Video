<?php

require_once 'includes.php';

ob_start();

$stdin =  stdin::input();
if (!empty($stdin->input) && !empty($stdin->id) && !empty($stdin->port))
{
        //$ffmpeg = new ffmpeg('rtsp://admin:*1234Hrs@192.168.2.15:554/11');
        $ffmpeg = new ffmpeg($stdin->input);
        $stream = new mkvStream($stdin->id, $stdin->port, $ffmpeg->pipeHandle);
}
else
{
        //print "incorrect inputs";
}

ob_flush();