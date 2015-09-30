<?php

require_once 'includes.php';

class stdsettings extends stdin
{
        public static $inputs = [
            '-a' => 'action',
            '-n' => 'name',
            '-j' => 'json',
            '-f' => 'file',
        ];
}

try
{
        
        $stdin = stdsettings::input();
        var_dump($stdin);
        $settings = new settings;

        if (!empty($stdin->action))
        {
                if ($stdin->action == 'push')
                {
                        print "CALLING PUSH!" . PHP_EOL;
                        $settings->pushSettings();
                }
                elseif ($stdin->action == 'pull')
                {
                        $settings->pullSettings();
                }
        }
        elseif (!empty($stdin->name) && !empty($stdin->json))
        {
                if ($settings->writeArrayToFile($stdin->name, json_decode($stdin->json)))
                {
                        $settings->pushSettings();
                }
        }
        elseif (!empty($stdin->file) && pathinfo($stdin->file)['extension'] == 'hrs' && file_exists($stdin->file))
        {
                if ($settings->writeArrayToFile(basename($stdin->file, '.hrs'), file_get_contents(json_decode($stdin->file))))
                {
                        $settings->pushSettings();
                }
        }
        elseif (!empty($_REQUEST['file']) && pathinfo($_REQUEST['file'])['extension'] == 'hrs' && file_exists($_REQUEST['file']))
        {
                if ($settings->writeArrayToFile(basename($_REQUEST['file'], '.hrs'), file_get_contents(json_decode($_REQUEST['file']))))
                {
                        $settings->pushSettings();
                }
        }
        elseif (!empty($_REQUEST['name']) && !empty($_REQUEST['json']))
        {
               if ($settings->writeArrayToFile($_REQUEST['json'], json_decode($_REQUEST['json'])))
               {
                       $settings->pushSettings();
               }
        }
}
catch (Exception $e)
{
        print "Exception: " . $e->getMessage() . PHP_EOL;
}