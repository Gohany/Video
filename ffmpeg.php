<?php

require_once 'stream.php';
require_once 'stdin.php';

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

class ffmpeg
{
        
        public $pipeHandle;
        
        public $defaultOptions = [
            'ffmpeg' => [
                'loglevel' => 'panic',
                'hide_banner',
                'nostats',
//                'y',
//                're',
            ],
            'output' => [
                'map_metadata' => '-1',
                'c:v' => 'libvpx',
                'vf' => 'scale=-1:320',
                'deadline' => 'good',
                'crf' => '7',
                'b:v' => '500k',
                'keyint_min' => '25',
                'g' => '7',
                'qmin' => '0',
                'qmax' => '50',
                'threads' => '4',
                'c:a' => 'libvorbis',
                'ar' => '44100',
                'ab' => '128k',
                'ac' => '1',
                'async' => '1',
//                'aframes' => '25',
//                'sample_fmt' => 'u8',
                'f' => 'webm',
            ],
        ];
        //ffmpeg -loglevel panic -hide_banner -nostats -i /var/www/h264.mp4 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 10 -b:v 500k -keyint_min 150 -g 150 -an -f webm -dash 1 pipe:1        -an -dash 1 rtsp://admin:*1234Hrs@192.168.2.15:554/11
        public function __construct($input, $options = array())
        {
                //$this->pipeHandle = popen('ffmpeg -loglevel panic -hide_banner -nostats -i rtsp://admin:*1234Hrs@192.168.2.7:554/11 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 7 -b:v 500k -keyint_min 15 -g 7 -c:a libvorbis -f webm pipe:1', 'r');
                $ffmpegString = $this->ffmpegString($input, $options);
                print $ffmpegString . PHP_EOL;
                $this->pipeHandle = popen($ffmpegString, 'r');
        }
        
        public function ffmpegString($input, $options)
        {
                
                $string = 'ffmpeg';
                
                $options = array_replace_recursive($this->defaultOptions, $options);
                foreach ($options['ffmpeg'] as $option => $value)
                {
                        $string .= ' -';
                        if (is_string($option))
                        {
                                $string .= $option . ' ';
                        }
                        $string .= $value;
                }
                
                $string .= ' -i ' . $input;
                
                foreach ($options['output'] as $option => $value)
                {
                        $string .= ' -';
                        if (is_string($option))
                        {
                                $string .= $option;
                        }
                        $string .= ' ' . $value;
                }
                
                $string .= ' pipe:1';
                return $string;
                
        }
        
        
}