<!DOCTYPE html>
<html >
    <head>
        <meta charset="UTF-8">
        <title>Raspberry Pi Weather Station dashboard</title>
        <link rel="stylesheet" href="dashboard.css">
        <script src='http://code.jquery.com/jquery-2.2.4.min.js'></script>
        <script src='http://www.gstatic.com/charts/loader.js'></script>
        <script src='dashboard.js'></script>
    </head>

    <body>
        <div id="chart_temp"></div>
        <div id="chart_hum"></div>
        <canvas id="wind_dir" width="200" height="200"></canvas>
        <div id="chart_pressure"></div>
        <div id="chart_pressure_change1h"></div>
        <div id="chart_pressure_change6h"></div>
        <div id="chart_pressure_change12h"></div>
        <div id="chart_pressure_change24h"></div>
        <div id="chart_pressure_change48h"></div>
        <div id="rawData"></div>
        <div id="footer">
            <div id="repo"><a href="https://github.com/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard">RPi Wx Dashboard</a></div>
            <div id="codeDate">
<?php
echo "\t\t\tUpdated on " . exec("git --git-dir=/var/www/html/dev/.git log -1 --pretty=format:'%ci'");
?>
            </div>
        </div>
    </body>
</html>