<?php

require_once 'includes.php';

class stdclient extends stdin
{
        public static $inputs = [
            '-n' => 'node',
            '-cl' => 'client',
            '-sn' => 'system_number',
            '-h' => 'macAddress',
        ];
}

$stdin = stdclient::input();

var_dump($stdin);

if (isset($stdin->client) && $stdin->client == 'node' && isset($stdin->node))
{
        $client = client::cmdNode($stdin->node);
}
elseif (isset($stdin->client) && $stdin->client = 'system')
{
        $client = new client('localhost', 'system');
}

try
{
        // php client.php -cl system -c SYSTEM_STOP_ALL
        // php client.php -cl system -c SYSTEM_STOP
        // php client.php -cl system -c SYSTEM_RESTART
        // php client.php -cl system -c SYSTEM_RESTART_ALL
        // php client.php -cl system -sn 1 -h "20:aa:4b:45:a7:76" -c BROADCAST_ASSIGN_NUMBER
        // php client.php -cl node -n 1 -id 1 -c NEW_SINGLE_SOURCE
        if (!isset($stdin->sid) && empty($stdin->sid))
        {
                $data = [];
                if (isset($stdin->id))
                {
                        $id = $stdin->id;
                        $data = [
                            'id' => $id,
                            'targets' => 'ALL',
                            'height' => 0,
                            'width' => 0,
                            'x' => 0,
                            'y' => 0,
                        ];
                }
                elseif (isset($stdin->system_number) && isset($stdin->macAddress))
                {
                        $data = [
                            'system_number' => $stdin->system_number,
                            'macAddress' => $stdin->macAddress,
                        ];
                }
                $client->request($stdin->cmd, $data);
        }
        else
        {
                $client->command('all', clientCmd::CMD_CHANGE_CHANNEL . ' mkv.' . $stdin->sid);
        }
}
catch (Exception $e)
{
        print $e->getMessage();
        die;
}
