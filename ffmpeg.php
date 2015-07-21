<?php

$ffmpeg = new ffmpeg('rtsp://admin:*1234Hrs@192.168.2.15:554/11', array('output' => array('crf' => '15')));

class ffmpeg
{
        
        public $pipeHandle;
        
        public $defaultOptions = [
            'ffmpeg' => [
                'loglevel' => 'panic',
                'hide_banner',
                'nostats',
            ],
            'output' => [
                'map_metadata' => '-1',
                'c:v' => 'libvpx',
                'vf' => 'scale=-1:320',
                'deadline' => 'realtime',
                'crf' => '7',
                'b:v' => '500k',
                'keyint_min' => '15',
                'g' => '7',
                'c:a' => 'libvorbis',
                'f' => 'webm',
            ],
        ];
        //ffmpeg -loglevel panic -hide_banner -nostats -i /var/www/h264.mp4 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 10 -b:v 500k -keyint_min 150 -g 150 -an -f webm -dash 1 pipe:1        -an -dash 1 rtsp://admin:*1234Hrs@192.168.2.15:554/11
        public function __construct($input, $options = array())
        {
                //$this->pipeHandle = popen('ffmpeg -loglevel panic -hide_banner -nostats -i rtsp://admin:*1234Hrs@192.168.2.7:554/11 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 7 -b:v 500k -keyint_min 15 -g 7 -c:a libvorbis -f webm pipe:1', 'r');
                $ffmpegString = $this->ffmpegString($input, $options);
                $this->pipeHandle = popen($ffmpegString);
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