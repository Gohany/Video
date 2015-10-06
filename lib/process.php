<?php

class Process
{

        public $pid;
        public $command;

        public function __construct($cl = false)
        {
                if ($cl != false)
                {
                        $this->command = $cl;
                        $this->runCom();
                }
        }

        private function runCom()
        {
                $command = 'nohup ' . $this->command . ' > /dev/null 2>&1 & echo $!';
                exec($command, $op);
                $this->pid = (int) $op[0];
        }

        public function setPid($pid)
        {
                $this->pid = $pid;
        }

        public function getPid()
        {
                return $this->pid;
        }

        public function status()
        {
                $command = 'ps -p ' . $this->pid;
                exec($command, $op);
                if (!isset($op[1]))
                {
                        return false;
                }
                else
                {
                        return true;
                }
        }
        
        public static function isRunning($cmd)
        {
                $command = "ps aux | grep \"[" . $cmd[0] . "]" . substr($cmd, 1) . "\"";
                exec($command, $op);
                return isset($op[0]);
        }
        
        public static function fromCMD($cmd)
        {
                $command = "ps aux | grep \"[" . $cmd[0] . "]" . substr($cmd, 1) . "\" | awk '{print $2;}'";
                exec($command, $op);
                if (count($op) > 0)
                {
                        foreach ($op as $pid)
                        {
                                $processes[$pid] = new Process();
                                $processes[$pid]->command = $cmd;
                                $processes[$pid]->setPid($pid);
                        }
                        return $processes;
                }
                return false;
        }
        
        public function start()
        {
                if ($this->command != '')
                {
                        $this->runCom();
                }
                else
                {
                        return true;
                }
        }

        public function stop()
        {
                $command = 'kill ' . $this->pid;
                exec($command);
                if ($this->status() == false)
                {
                        return true;
                }
                else
                {
                        return false;
                }
        }

}