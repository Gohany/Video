<?php

foreach (glob('/var/www/*.php') as $phpFile)
{
        require_once $phpFile;
}