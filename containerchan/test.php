<?php

	require_once 'videodata.php';
	require_once 'matroska.php';

	$data = array();
        //$filename = '/var/www/output1.webm';
        // Open file
        //$fileHandle = fopen($filename, 'rb');
        //$fileHandle = popen('ffmpeg -loglevel panic -hide_banner -nostats -i /var/www/h264.mp4 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 10 -b:v 500k -keyint_min 150 -g 150 -an -f webm -dash 1 pipe:1', 'r');-an -dash 1
        $fileHandle = popen('ffmpeg -loglevel panic -hide_banner -nostats -i rtsp://admin:*1234Hrs@192.168.2.15:554/11 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 7 -b:v 500k -keyint_min 15 -g 7 -c:a libvorbis -f webm pipe:1', 'r');
        //ffmpeg -i h264.mp4 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 20 -b:v 500k -keyint_min 150 -g 150 -c:a libvorbis -f webm -dash 1 output1.webm
        if (!$fileHandle)
        {
                error_log('could not open file');
                return $data;
        }

        try
        {
                readAll($fileHandle);     
        }
        catch (Exception $e)
        {
                print $e->getMessage();
        }

?>
