<?php
include 'variables.php';
header("access-control-allow-origin: *");

$con = new mysqli($databaseAddress,$databaseUsername,$databasePassword,$databaseSchema);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

/*
SELECT SUM(Rainfall) FROM WEATHER_MEASUREMENT WHERE created >= DATEADD(HOUR, -1, GETDATE()) INTO @rainPastHour;

SELECT SUM(Rainfall) FROM WEATHER_MEASUREMENT WHERE created >= DATE(NOW()) INTO @rainSinceMidnight;

SELECT WIND_DIRECTION, WIND_SPEED, WIND_GUST_SPEED, HUMIDITY, AMBIENT_TEMPERATURE, @rainPastHour, @rainSinceMidnight, AIR_PRESSURE, GROUND_TEMPERATURE FROM WEATHER


*/

echo "http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?ID=&PASSWORD=&softwaretype=N5JLC%20Raspberry%20Pi%20Wx%20Dashboard&";

$sql = "SELECT SUM(Rainfall) as rainPastHour FROM WEATHER_MEASUREMENT WHERE created >= DATEADD(HOUR, -1, GETDATE());";
$result = mysql_query($sql);
$value = mysql_fetch_object($result);
$rainPastHour = $value->rainPastHour;
echo "rainin=" . $rainPastHour . "&";
    

$sql = "SELECT SUM(Rainfall) as rainSinceMidnight FROM WEATHER_MEASUREMENT WHERE created >= DATE(NOW());";
$result = mysql_query($sql);
$value = mysql_fetch_object($result);
$rainSinceMidnight = $value->rainSinceMidnight;
echo "dailyrainin=" . $rainSinceMidnight . "&";

$result = $con->query('SELECT WIND_DIRECTION, WIND_SPEED, WIND_GUST_SPEED, HUMIDITY, AMBIENT_TEMPERATURE, AIR_PRESSURE, GROUND_TEMPERATURE FROM WEATHER_MEASUREMENT ORDER BY CREATED DESC LIMIT 1');

if ($result->num_rows > 0) {
    $row = mysqli_fetch_array($result);
    
    echo "winddir=" . $row["WIND_DIRECTION"] . "&";
    echo "windspeedmph=" . $row["WIND_SPEED"] . "&";
    echo "windgustmph=" . $row["WIND_GUST_SPEED"] . "&";
    echo "humidity=" . $row["HUMIDITY"] . "&";
    echo "tempf=" . $row["AMBIENT_TEMPERATURE"] . "&";
    echo "baromin=" . $row["AIR_PRESSURE"] . "&";
    echo "soiltempf " . $row["GROUND_TEMPERATURE"] . "&";
    
    $result->close();
    $con->next_result();
}

$result->close();
mysqli_close($con);

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
