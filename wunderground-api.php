<?php
include 'variables.php';
header("access-control-allow-origin: *");

$con = new mysqli($databaseAddress,$databaseUsername,$databasePassword,$databaseSchema);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

echo "http://weatherstation.wunderground.com/weatherstation/updateweatherstation.php?ID=&PASSWORD=&softwaretype=N5JLC%20Raspberry%20Pi%20Wx%20Dashboard&";

$result = $con->query('call GETWUNDERGROUNDDATA');

if ($result->num_rows > 0) {
    $row = mysqli_fetch_array($result);
    
    echo "winddir=" . $row["WIND_DIRECTION"] . "&";
    echo "windspeedmph=" . $row["WIND_SPEED"] . "&";
    echo "windgustmph=" . $row["WIND_GUST_SPEED"] . "&";
    echo "humidity=" . $row["HUMIDITY"] . "&";
    echo "tempf=" . $row["AMBIENT_TEMPERATURE"] . "&";
    echo "baromin=" . $row["AIR_PRESSURE"] . "&";
    echo "soiltempf=" . $row["GROUND_TEMPERATURE"] . "&";
    echo "rainin=" . $row["@rainPastHour"] . "&";
    echo "dailyrainin=" . $row["@rainSinceMidnight"] . "&";
    
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
