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
                <script src="//code.jquery.com/jquery-1.10.2.js"></script>
                <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
                <style>
                        .draggable {
                                float:left;
                                padding: 5px;
                                background-color: black;
                        }
                </style>
        </head>
        <body>
                <div id="container1" class="draggable ui-widget-content">
                        <video id="video1" autoplay muted controls>
                                <source src="http://127.0.0.2/vListen.php?id=<?=$id?>&sid=<?=session_id()?>">
                        </video>        
                </div>
            <!--
                <div id="container2" class="draggable ui-widget-content">
                        <video id="video2" autoplay muted>
                                <source src="http://127.0.0.2/vListen.php?id=<?=$id?>" type="video/webm">
                        </video>
                </div>
                <div id="container3" class="draggable ui-widget-content">
                        <video id="video3" autoplay muted>
                                <source src="http://127.0.0.2/vListen.php?id=<?=$id?>" type="video/webm">
                        </video>
                </div>
                <div id="container4" class="draggable ui-widget-content">
                        <video id="video4" autoplay muted>
                                <source src="http://127.0.0.2/vListen.php?id=<?=$id?>" type="video/webm">
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
                                        resize: function(event, ui){
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
                </script>
        </body>
</html>
