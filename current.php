<?php
include 'database.php';
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
$con = new mysqli($databaseAddress,$databaseUsername,$databasePassword, $databaseSchema);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = $con->query('call GETRECENTOBS');
$fieldcount = mysqli_num_fields($result);

echo "{ "; // Open document object
echo "\r\n\t\"WeatherObservations\" : {"; // Open weather observations object

if ($result->num_rows > 0) {
    $fields = array();
    while ($fieldinfo = mysqli_fetch_field($result)) {
        array_push($fields, $fieldinfo->name);
    }

    $numberOfRows = 0;
    while($row = mysqli_fetch_array($result)) { // Rows
	   $numberOfRows++;

        if ($numberOfRows > 1) {
            echo ", ";
        }

        echo "\r\n\t\t\"Observation" . $numberOfRows . "\" : {";
        
        if ($numberOfRows == 1) {
            $feelsLike = calculateFeelsLike($row["AMBIENT_TEMPERATURE"], $row["HUMIDITY"], $row["WIND_SPEED"]);
            if (!$showMetricAndCelsiusMeasurements == "1") {
                $feelsLike = convertCelsiusToFahrenheit($feelsLike);
            }
            echo "\r\n\t\t\t\"FEELS_LIKE\" : " . "\"" . $feelsLike . "\",";
        }
        
        for ($i = 0; $i < $fieldcount; $i++) {   // Columns
            $fieldName = $fields[$i];
            $fieldValue = $row[$i];
            
            if (strpos($fieldName, "_TEMPERATURE")) {
                if ($showMetricAndCelsiusMeasurements == "1") {
                    echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° C\",";
                }
                else {
                    $fieldValue = convertCelsiusToFahrenheit($fieldValue);
                    echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° F\",";
                }
            }

            if (strpos($fieldName, "_SPEED")) {
                if ($showMetricAndCelsiusMeasurements == "1") {
                    echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . " Km/H\",";
                }
                else {
                    $fieldValue = convertKilometersToMiles($fieldValue);
                    echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . " MPH\",";
                }
            }

            if (strpos($fieldName, "_PRESSURE")) {
                if ($showPressureInMillibars == "1") {
                    echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . " mb\",";
                }
                else {
                    $fieldValue = convertMillibarsToInches($fieldValue);
                    echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . " in\",";
                }
            }

            echo "\r\n\t\t\t\"" . $fieldName . "\" : " . "\"" . $fieldValue . "\"";

            if ($i+1 < $fieldcount) {
                    echo ",";
            }
        }
        
        echo "\r\n\t\t}";
    }
    
    $result->close();
    $con->next_result();
}
echo "\r\n\t}"; // Close weather observations object

$result = $con->query('call GETDAILYRECORDS');
$fieldcount = mysqli_num_fields($result);
echo ",\r\n\t\"DailyStats\" : {"; // Open daily stats object

if ($result->num_rows > 0) {
    $fields = array();
    while ($fieldinfo = mysqli_fetch_field($result)) {
        array_push($fields, $fieldinfo->name);
    }

    $row = mysqli_fetch_array($result); // Only one row of data
    for ($i = 0; $i < $fieldcount; $i++) {   // Columns
        $fieldInfo = mysqli_fetch_field($result);
        $fieldName = $fields[$i];
        $fieldValue = $row[$i];

        if (($fieldName == "LowSinceMidnight") || ($fieldName == "HighSinceMidnight")) {
            if ($showMetricAndCelsiusMeasurements == "1") {
                echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° C\",";
            }
            else {
                $fieldValue = convertCelsiusToFahrenheit($fieldValue);
                echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° F\",";
            }
        }
        
        echo "\r\n\t\t\"" . $fieldName . "\" : " . "\"" . $fieldValue . "\"";

        if ($i+1 < $fieldcount) {
            echo ",";
        }
    }
}
echo "\r\n\t}"; // Close daily stats object

echo ",\r\n\t\"Settings\" : {"; // Open settings object
echo "\r\n\t\t\"showMetricAndCelsiusMeasurements\" : " . "\"" . $showMetricAndCelsiusMeasurements . "\",";
echo "\r\n\t\t\"showPressureInMillibars\" : " . "\"" . $showPressureInMillibars . "\"";

echo "\r\n\t}"; // Close settings object

echo "\r\n}"; // Close document object

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

function convertFahrenheitToCelsius($fahrenheitDegrees) {
    $C = ($fahrenheitDegrees - 32) * 5 / 9;
    return $C;
}

function convertMillibarsToInches($millibars) {
    $inches = $millibars * 0.0295301;
    return $inches;
}

function calculateFeelsLike($temperature, $humidity, $windSpeed) {
    $tempF = convertCelsiusToFahrenheit($temperature);
    $windMPH = convertKilometersToMiles($windSpeed);
    
    // Calculate Heat Index based on temperature in F and relative humidity (65 = 65%)
    if ($tempF > 70 && $humidity > 39) { 
        $feelsLike = -42.379 + 2.04901523 * $tempF + 10.14333127 * $humidity - 0.22475541 * $tempF * $humidity;
        $feelsLike += -0.00683783 * pow($tempF, 2) - 0.05481717 * pow($humidity, 2);
        $feelsLike += 0.00122874 * pow($tempF, 2) * $humidity + 0.00085282 * $tempF * pow($humidity, 2);
        $feelsLike += -0.00000199 * pow($tempF, 2) * pow($humidity, 2);
        $feelsLike = round($feelsLike);
    }
    elseif (($tempF < 60) && ($windMPH > 3)) {
        $feelsLike = 35.74 + 0.6215 * $tempF - 35.75 * pow($windMPH, 0.16) + 0.4275 * $tempF * pow($windMPH, 0.16);
        $feelsLike = round($feelsLike);
    }
    else {
        $feelsLike = $tempF;
    }
    
    return convertFahrenheitToCelsius($feelsLike);
}

?>
