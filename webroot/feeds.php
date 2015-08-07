<?php
        require_once '/var/www/feeds.php';
        $feeds = feeds::byLatest(20);
?>
<!DOCTYPE html>
<html>
<head>
    <title></title>

    <!-- Ignite UI Required Combined CSS Files -->
    <link href="http://cdn-na.infragistics.com/igniteui/2015.1/latest/css/themes/infragistics/infragistics.theme.css" rel="stylesheet" />
    <link href="http://cdn-na.infragistics.com/igniteui/2015.1/latest/css/structure/infragistics.css" rel="stylesheet" />

    <script src="http://modernizr.com/downloads/modernizr-latest.js"></script>
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>

    <!-- Ignite UI Required Combined JavaScript Files -->
    <script src="http://cdn-na.infragistics.com/igniteui/2015.1/latest/js/infragistics.core.js"></script>
    <script src="http://cdn-na.infragistics.com/igniteui/2015.1/latest/js/infragistics.lob.js"></script>

    <style>
        #grid_container
        {
            width: 100%;
        }
    </style>

</head>
<body>

    <table id="grid"></table>

    <script>
        $(function () {

            var data = <?php
                print json_encode($feeds->feeds);
            ?>
            
            $("#grid").igGrid({
                dataSource: data //JSON Array defined above                     
            });
        });
    </script>

</body>
</html>