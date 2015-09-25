<?php

class zmqPorts
{
        
        const PROXY_PORT =                              8100;
        const CONTROLLER_VSYNC_INSTRUCTION =            'backend';
        const VSYNC_WEBSOCKET_INSTRUCTION =             5557;
        const CLIENT_WEBSOCKET_INSTRUCTION =            'clientInstruction';
        const CLIENT_VLISTEN_INSTRUCTION =              8101;
        const CLIENT_CONTROLLER_INSTRUCTION =           6200;
        const DEFAULT_STREAM_PORT =                     5556;
        const CONTROLLER_WEBSOCKET_INSTRUCTION =        'websocketInstruction';
        const NETWORK_DISCOVERY_PORT_IN =               7575;
        const NETWORK_DISCOVERY_PORT_OUT =              7575;
        
        const CONTROLLER_VSYNC_PROTOCOL =               'ipc';
        const CLIENT_CONTROLLER_PROTOCOL =              'tcp';
        const PROXY_PORT_PROTOCOL =                     'tcp';
        const DEFAULT_STREAM_PROTOCOL =                 'tcp';
        const CLIENT_WEBSOCKET_PROTOCOL =               'ipc';
        const CLIENT_VLISTEN_PROTOCOL =                 'tcp';
        const CONTROLLER_WEBSOCKET_PROTOCOL =           'ipc';
        const NETWORK_DISCOVERY_PROTOCOL =              'tcp';
        
}

