<?php

class streams
{
        
        public $resources;
        
        //'ffmpeg -loglevel panic -hide_banner -nostats -i rtsp://admin:*1234Hrs@192.168.2.7:554/11 -map_metadata -1 -c:v libvpx -vf scale=-1:320 -deadline realtime -crf 7 -b:v 500k -keyint_min 15 -g 7 -c:a libvorbis -f webm pipe:1'
        public function start($id, $string)
        {
                $this->resources[$id] = popen($string, 'r');
        }
        
        public function run()
        {
                
                if (!empty($this->resources))
                {
                        foreach ($this->resources as $id => $resource)
                        {
                                
                                
                                
                        }
                }
                
        }
        
}