<?php

	require_once 'videodata.php';
	require_once 'matroska.php';

	$data = array();
        //$filename = '/var/www/output1.webm';
        // Open file
        //$fileHandle = fopen($filename, 'rb');
        $fileHandle = popen('ffmpeg -loglevel panic -hide_banner -nostats -i /var/www/h264.mp4 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 10 -b:v 500k -keyint_min 150 -g 150 -an -f webm -dash 1 pipe:1', 'r');
        //ffmpeg -i h264.mp4 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 20 -b:v 500k -keyint_min 150 -g 150 -c:a libvorbis -f webm -dash 1 output1.webm
        if (!$fileHandle)
        {
                error_log('could not open file');
                return $data;
        }

        try
        {
                
                readAll($fileHandle);
                
//                $root = readMatroska($fileHandle);
//                $data['container'] = $root->get('EBML')->get('DocType');
//                $segment = $root->get('Segment');
//                
//                $info = $segment->get('Info');
//                $timecodeScale = $info->get('TimecodeScale');
//                $duration = $info->get('Duration');
                //print "timecodescale: ".$timecodeScale.PHP_EOL;
                //print "duration: ".$duration.PHP_EOL;
                
                //$tracks = $segment->get('Tracks');
                
//                while($next = $segment->next())
//                {
//                        foreach ($next as $nextElement)
//                        {
//                                print "ENTRY NAME: ".$nextElement->name() . PHP_EOL;
//                        }
//                }
                
//                foreach ($segment as $element)
//                {
//                        if ($element->name() == 'Cluster')
//                        {
//                                
//                        }
//                }
                
//                foreach ($segment as $track)
//                {
//                        print "ENTRY NAME: ".$track->name(). PHP_EOL;
//                        if ($track->name() == 'Cluster')
//                        {
//                                var_dump($track);
//                                if (is_object($track->value()))
//                                {
//                                        var_dump($track->value());
//                                }
//                        }
//                        else
//                        {
//                                print "unexpected: ".$track->name() . PHP_EOL;
//                        }
//                        foreach ($track as $trackElements)
//                        {
////                                print "SEGMENT ELEMENT: ".$trackElements->name() . PHP_EOL;
////                                if ($trackElements->name() == 'Timecode' || $trackElements->name() == 'SimpleBlock')
////                                {
////                                        $value = $trackElements->value();
////                                        if (is_object($value))
////                                        {
////                                                print "VALUE: " . $value->readAll() . PHP_EOL;
////                                        }
////                                        else
////                                        {
////                                                print "VALUE: " . $value . PHP_EOL;
////                                        }
////                                }
//                                foreach ($trackElements as $subelement)
//                                {
//                                        print "SUB ELEMENT: ".$subelement->name() . PHP_EOL;
//                                }
//                        }
                //}
                
                
                
        }
        catch (Exception $e)
        {
                print $e->getMessage();
        }

?>
