<?php

class clientCmd
{
        const CMD_CHANGE_CHANNEL = 1;
        const CMD_STOP = 2;
        const CMD_ADD_CHANNEL = 3;
        const CMD_REMOVE_CHANNEL = 4;
        
        // add another source to current viewing
        const ADD_SOURCE = 5;
        // make new source the only source
        const NEW_SINGLE_SOURCE = 6;
        // remove a viewed source
        const REMOVE_SOURCE = 7;
        // make current source the only source
        const SINGLE_SOURCE = 8;
        // make blank
        const REMOVE_ALL = 9;
        
}

class vSyncCmd
{
        
        const START_FFMPEG = 1;
        
}