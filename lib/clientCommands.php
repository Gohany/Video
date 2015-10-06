<?php

class requestCmd
{
        const ADD_SOURCE = 16;
        // make new source the only source
        const NEW_SINGLE_SOURCE = 17;
        // remove a viewed source
        const REMOVE_SOURCE = 18;
        // make current source the only source
        const SINGLE_SOURCE = 19;
        // make blank
        const REMOVE_ALL = 20;
        const PUBLISH = 21;
        const REMOVE_PORT = 22;
        const START_ENCODE = 23;
        const SUBSCRIBE = 24;
        const START_DISPLAY = 27;
        const REPLY_CLIENT = 28;
        const SUCCESS = 29;
        const STOP_DISPLAY = 30;
        const UNSUBSCRIBE = 31;
        const STOP_ENCODE = 32;
        const HELLO = 33;
        const START_NEW_DISPLAY = 34;
        const ADD_LAYER = 35;
        const REMOVE_LAYER = 36;
        const MOVE_LAYER = 37;
        
        const ASSIGN_SYSTEM_NUMBER = 38;
        const BROADCAST_ASSIGN_NUMBER = 39;
        const SYSTEM_STOP = 40;
        const SYSTEM_STOP_ALL = 41;
        const SYSTEM_RESTART = 42;
        const SYSTEM_RESTART_ALL = 43;
        
}