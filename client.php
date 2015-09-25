<?php

require_once 'includes.php';

$stdin = stdin::input();

$client = client::cmdNode($stdin->node);

try
{
        if (!isset($stdin->sid) && empty($stdin->sid))
        {
                $id = $stdin->id;
                $client->request($stdin->cmd, $stdin->id);
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
