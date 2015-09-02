<?php

if (PHP_SAPI !== 'cli')
{
	die('This class is only available from command-line.' . PHP_EOL);
}

class stdin
{

        protected static $defaultInputs = [
            '-i' => 'input',
            '-id' => 'id',
            '-p' => 'port',
            '-sid' => 'sid',
            '-c' => 'cmd',
            '-w' => 'who',
            '-wb' => 'whoBy',
        ];
        protected static $inputs = [
        ];
        
        public $input;
        public $id;
        public $port;
        public $sid;
        public $cmd;
        public $who;

        public function __construct($inputs = array())
        {
                foreach ($inputs as $name => $value)
                {
                        $this->{$name} = $value;
                }
        }

        public static function input()
        {
                $current = '';
                $inputs = array();
                for ($c = count($_SERVER['argv']) - 1, $i = 1; $c >= $i; $i++)
                {
                        if (!empty(static::$inputs[$_SERVER['argv'][$i]]))
                        {
                                $current = static::$inputs[$_SERVER['argv'][$i]];
                        }
                        elseif (!empty(self::$defaultInputs[$_SERVER['argv'][$i]]))
                        {
                                $current = self::$defaultInputs[$_SERVER['argv'][$i]];
                        }
                        elseif (!empty($current))
                        {
                                if (empty($inputs[$current]))
                                {
                                        $inputs[$current] = trim($_SERVER['argv'][$i]);
                                }
                                else
                                {
                                        $inputs[$current] .= ' ' . trim($_SERVER['argv'][$i]);
                                }
                        }
                }
                return new stdin($inputs);
        }

}
