<?php

class stdin
{
        public static $inputs = [
            '-i' => 'input',
            '-id' => 'id',
            '-p' => 'port',
            '-sid' => 'sid',
            '-c' => 'cmd',
            '-w' => 'who',
        ];
        
        public function __construct()
        {
                $current = '';
                for ($c = count($_SERVER['argv']) - 1, $i = 1; $c >= $i; $i++)
                {
                        if (!empty(self::$inputs[$_SERVER['argv'][$i]]))
                        {
                                $current = self::$inputs[$_SERVER['argv'][$i]];
                        }
                        elseif (!empty($current))
                        {
                                if (empty($this->{$current}))
                                {
                                        $this->{$current} = trim($_SERVER['argv'][$i]);
                                }
                                else
                                {
                                        $this->{$current} .= ' ' . trim($_SERVER['argv'][$i]);
                                }
                        }
                }
        }
}