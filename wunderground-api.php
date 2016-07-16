<?php
include 'variables.php';
header("access-control-allow-origin: *");

$con = new mysqli($databaseAddress,$databaseUsername,$databasePassword,$databaseSchema);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$url = "http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?softwaretype=N5JLC Raspberry Pi Wx Dashboard&";

$result = $con->query('call GETWUNDERGROUNDDATA');
if ($result->num_rows > 0) {
    $row = mysqli_fetch_array($result);
    
    $url .= "dateutc=" . $row["CREATEDUTC"] . "&";
    $url .= "winddir=" . $row["WIND_DIRECTION"] . "&";
    $url .= "windspeedmph=" . $row["WIND_SPEED"] . "&";
    $url .= "windgustmph=" . $row["WIND_GUST_SPEED"] . "&";
    $url .= "humidity=" . $row["HUMIDITY"] . "&";
    $url .= "tempf=" . $row["AMBIENT_TEMPERATURE"] . "&";
    $url .= "baromin=" . $row["AIR_PRESSURE"] . "&";
    $url .= "soiltempf=" . $row["GROUND_TEMPERATURE"] . "&";
    $url .= "rainin=" . $row["@rainPastHour"] . "&";
    $url .= "dailyrainin=" . $row["@rainSinceMidnight"] . "&";
    $url .= "ID=" . $row["@WUNDERGROUND_ID"] . "&";
    $url .= "PASSWORD=" . $row["@WUNDERGROUND_PASSWORD"] . "&";
}

$result->close();
$con->close();

$url = str_replace(" ", "%20", $url);

echo file_get_contents($url);

// ===============================================================
function convertKilometersToMiles($kilometers) {
    $miles = $kilometers * 0.621371;
    return $miles;
}
        
function convertCelsiusToFahrenheit($celsiusDegrees) {
    $F = ((($celsiusDegrees * 9) / 5) + 32);
    return $F;
}

function convertMillibarsToInches($millibars) {
    $inches = $millibars * 0.0295301;
    return $inches;
}


?>
