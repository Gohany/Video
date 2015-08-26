<?php
session_start();
$id = !empty($_GET['id']) ? $_GET['id'] : 1;
$_SESSION['currentId'] = $id;
$_SESSION['pid'] = getmypid();
?>
<!DOCTYPE html>
<html manifest="manifest.appcache">
        <head>
                <title>SW</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
                <meta http-equiv="Pragma" content="no-cache" />
                <meta http-equiv="Expires" content="0" />
                <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
                <link href="video-js.css" rel="stylesheet" type="text/css">
                <!-- video.js must be in the <head> for older IEs to work. -->
                <script src="video.js"></script>
                <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
                <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
                <style>
                        .draggable {
                                float:left;
                        }
                        #log {
                                width:600px; 
                                height:300px; 
                                border:1px solid #7F9DB9; 
                                overflow:scroll;
                                float:right;
                        }
                </style>
                <script type="text/javascript">

                        function vidSwap(vidURL) {
                                var myVideo = videojs('video1');
                                myVideo.src(vidURL);
                                myVideo.loop(true);
                                
                                myVideo.onerror = function()
                                {
                                        console.log(myVideo.error);
                                        myVideo.load();
                                        myVideo.play();
                                }
                                
                                myVideo.load();
                                myVideo.play();
                        }
                        
                        function swap(vidURL)
                        {
                                
                                var vid1 = videojs('video1');
                                var vid2 = videojs('video2');
                                
                                vid2.src(vidURL);
                                vid2.loop(true);
                                
                                vid2.onerror = function()
                                {
                                        console.log(vid2.error);
                                        vid2.load();
                                        vid2.play();
                                }
                                
                                vid2.load();
                                vid2.play();
                                
                                vid2.on('loadeddata', function(){
                                        document.getElementById('container2').style.display = "block";
                                        document.getElementById('container1').style.display = "none";
                                        vid1.pause();
                                        vid2.muted(false);
                                        vid1.dispose();
                                });
                                
                        }
                        
                        function sync(time)
                        {
                                if (time)
                                {
                                        var vid = videojs('video1');
                                        var difference = vid.currentTime() - time;
                                        vid.pause();
                                        console.log('PAUSING FOR ' + difference + ' SECONDS');
                                        window.setTimeout(function(){
                                                vid.play();
                                                console.log('PLAYING..');
                                        }, (difference * 1000));
                                }
                        }

                        var socket;
                        socket.readyState = '0';
                        function init() {
                                var host = "ws://192.168.2.6:9000/clientWS"; // SET THIS TO YOUR SERVER
                                try {
                                        socket = new WebSocket(host);
                                        log('WebSocket - status ' + socket.readyState);
                                        socket.onopen = function (msg) {
                                                log("Welcome - status " + this.readyState);
                                                console.log("OPENING MESSAGE: " + msg);
                                                log("MSG: " + msg);
                                        };
                                        socket.onmessage = function (msg) {
                                                log("Received: " + msg.data);
                                                //console.log(msg);
                                                var commandSet = msg.data.split('|');
                                                //console.log(commandSet);
                                                if (commandSet[0] == 'swap')
                                                {
                                                        swap(commandSet[1]);
                                                }
                                                else if (commandSet[0] == 'sync')
                                                {
                                                        sync(commandSet[1]);
                                                }
                                                else
                                                {
                                                        log('COMMAND: ' + msg.data);
                                                }
                                        };
                                        socket.onclose = function (msg) {
                                                log("Disconnected - status " + this.readyState);
                                        };
                                }
                                catch (ex) {
                                        log(ex);
                                }
                                $("msg").focus();
                        }
                        function send(msg) {
                                if (socket.readyState == '1')
                                {
                                        if (!msg) {
                                                alert("Message can not be empty");
                                                return;
                                        }
                                        try {
                                                socket.send(msg);
                                                log('Sent: ' + msg);
                                        } catch (ex) {
                                                log(ex);
                                        }
                                }
                        }
                        function quit() {
                                if (socket != null) {
                                        log("Goodbye!");
                                        socket.close();
                                        socket = null;
                                }
                        }
                        function reconnect() {
                                quit();
                                init();
                        }
                        // Utilities
                        function $(id) {
                                return document.getElementById(id);
                        }
                        function log(msg) {
                                $("log").innerHTML += "<br>" + msg;
                        }
                        function onkey(event) {
                                if (event.keyCode == 13) {
                                        send();
                                }
                        }
                </script>
        </head>
        <body onload="init()">
                <div id="container1" style="position:absolute; left: 0px; top: 0px;">
                        <video id="video1" class="video-js vjs-default-skin" preload="none" muted autoplay controls height='320px' width='753px' data-setup="{}">
                                <source type='video/webm' src="http://<?= $_SERVER['SERVER_ADDR'] ?>/vListen.php?id=<?= $id ?>&sid=<?= session_id() ?>">
                        </video>
                </div>
                <div id="container2" style="display:none; position:absolute; left: 0px; top: 0px;">
                        <video id="video2" class="video-js vjs-default-skin" preload="none" muted controls height='320px' width='753px' data-setup="{}">
                                <source type='video/webm' src="http://<?= $_SERVER['SERVER_ADDR'] ?>/vListen.php?id=<?= $id ?>&sid=<?= session_id() ?>">
                        </video>
                </div>
                <div>
                        
                        <div id="log"></div>
                        <div style="float:right;">
                                
                                <!--<input id="msg" type="textbox" onkeypress="onkey(event)"/>
                                <button onclick="send()">Send</button>
                                <button onclick="quit()">Quit</button>-->
                                <button onclick="reconnect()">Reconnect</button><br />
                                <a href="http://<?= $_SERVER['SERVER_ADDR'] ?>/vListen.php?id=<?= $id ?>&sid=<?= session_id() ?>">Download</a>
                        </div>
                </div>
                <!--
                    <div id="container2" class="draggable ui-widget-content">
                            <video id="video2" autoplay muted>
                                    <source src="http://127.0.0.2/vListen.php?id=<?= $id ?>" type="video/webm">
                            </video>
                    </div>
                    <div id="container3" class="draggable ui-widget-content">
                            <video id="video3" autoplay muted>
                                    <source src="http://127.0.0.2/vListen.php?id=<?= $id ?>" type="video/webm">
                            </video>
                    </div>
                    <div id="container4" class="draggable ui-widget-content">
                            <video id="video4" autoplay muted>
                                    <source src="http://127.0.0.2/vListen.php?id=<?= $id ?>" type="video/webm">
                            </video>
                    </div>
                -->
                <script>
                        $(function () {
                                $("#container1").draggable();
                                //$("#container2").draggable();
                                //$("#container3").draggable();
                                //$("#container4").draggable();
                                $("#container1").resizable({
                                        aspectRatio: true,
                                        resize: function (event, ui) {
                                                $("#video1").width(ui.size.width);
                                                $("#video1").height(ui.size.height);
                                        }
                                });
                                /*
                                 $("#container2").resizable({
                                 aspectRatio: true,
                                 resize: function(event, ui){
                                 $("#video2").width(ui.size.width);
                                 $("#video2").height(ui.size.height);
                                 }
                                 });
                                 $("#container3").resizable({
                                 aspectRatio: true,
                                 resize: function(event, ui){
                                 $("#video3").width(ui.size.width);
                                 $("#video3").height(ui.size.height);
                                 }
                                 });
                                 $("#container4").resizable({
                                 aspectRatio: true,
                                 resize: function(event, ui){
                                 $("#video4").width(ui.size.width);
                                 $("#video4").height(ui.size.height);
                                 }
                                 });
                                 */
                        });
                        var player = videojs('video1');
                        player.on('timeupdate', function(){
                                //console.log(player.currentTime());
                                send("<?=$id?>|" + player.currentTime());
                        });
                </script>
        </body>
</html>