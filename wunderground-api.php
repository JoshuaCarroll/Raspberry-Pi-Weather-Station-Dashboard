<?php

// ================================================================
//
//   For details about the Weather Underground personal weather
//   station API, visit: 
//
//   http://wiki.wunderground.com/index.php/PWS_-_Upload_Protocol
//
// ================================================================

include 'database.php';
header("access-control-allow-origin: *");

function main() {
    $con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    $url = "http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?softwaretype=Raspberry Pi Weather Station Dashboard&";
    $stationPassword = "";

    $result = $con->query('call GETWUNDERGROUNDDATA');
    if ($result->num_rows > 0) {
        $row = mysqli_fetch_array($result);

        $url .= "dateutc=" . $row["CREATEDUTC"] . "&";
        $url .= "winddir=" . $row["WIND_DIRECTION"] . "&";
        $url .= "windspeedmph=" . convertKilometersToMiles($row["WIND_SPEED"]) . "&";
        $url .= "windgustmph=" . convertKilometersToMiles($row["WIND_GUST_SPEED"]) . "&";
        $url .= "humidity=" . $row["HUMIDITY"] . "&";
        $url .= "tempf=" . convertCelsiusToFahrenheit($row["AMBIENT_TEMPERATURE"]) . "&";
        $url .= "dewptf=" . calculateDewPointF($row["AMBIENT_TEMPERATURE"], $row["HUMIDITY"]) . "&";
        $url .= "baromin=" . convertMillibarsToInches(calculateMeanSeaLevelPressure($row["AIR_PRESSURE"], Settings::$stationElevationInMeters)) . "&";
        $url .= "soiltempf=" . convertCelsiusToFahrenheit($row["GROUND_TEMPERATURE"]) . "&";
        $url .= "rainin=" . convertmillimetersToInches($row["@rainPastHour"]) . "&";
        $url .= "dailyrainin=" . convertmillimetersToInches($row["@rainSinceMidnight"]) . "&";
        $url .= "ID=" . $row["@WUNDERGROUND_ID"] . "&";
        $url .= "PASSWORD=";
        $stationPassword = $row["@WUNDERGROUND_PASSWORD"];
    }

    $result->close();
    $con->close();

    $url = str_replace(" ", "%20", $url);

    if (isset($_GET['debug'])) {
        $url .= "HIDDEN-IN-DEBUG_MODE" . "&";
        echo $url;
    }
    else {
        $url .= $stationPassword . "&";
        echo file_get_contents($url);
    }
}

main();

?>
