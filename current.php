<?php
include 'variables.php';
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
$con = new mysqli($databaseAddress,$databaseUsername,$databasePassword,'weather');

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

	for ($i = 0; $i < $fieldcount; $i++) {   // Columns
		$fieldName = $fields[$i];
		$fieldValue = $row[$i];
        
        
        if (strpos($fieldName, "_TEMPERATURE")) {
            if ($showMetricAndCelsiusMeasurements) {
                echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° C\",";
            }
            else {
                $fieldValue = convertCelsiusToFahrenheit($fieldValue);
                echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . "° F\",";
            }
        }
        
        if (strpos($fieldName, "_SPEED")) {
            if ($showMetricAndCelsiusMeasurements) {
                echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . " Km/H\",";
            }
            else {
                $fieldValue = convertKilometersToMiles($fieldValue);
                echo "\r\n\t\t\t\"" . $fieldName . "_STRING\" : " . "\"" . $fieldValue . " MPH\",";
            }
        }
        
        if (strpos($fieldName, "_PRESSURE")) {
            if ($showPressureInMillibars) {
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
            if ($showMetricAndCelsiusMeasurements) {
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

function convertMillibarsToInches($millibars) {
    $inches = $millibars * 0.0295301;
    return $inches;
}

function calculateHeatIndex($relativeHumidity, $airTemperature) {
    $airTemperature = convertCelsiusToFahrenheit($airTemperature);
    
    if ($relativeHumidity > 100){
        $relativeHumidity = 100;
    }
    else if ($relativeHumidity < 0) {
        $relativeHumidity = 0;
    }
    
    if ($airTemperature <= 40.0) {
        $hi = $airTemperature;
    }
    else {
        $hitemp = 61.0+(($airTemperature-68.0)*1.2)+($relativeHumidity*0.094);
        $fptemp = $airTemperature;
        $hifinal = 0.5*($fptemp+$hitemp);

        if ($hifinal > 79.0) {
            $hi = -42.379+2.04901523*$airTemperature+10.14333127*$relativeHumidity-0.22475541*$airTemperature*$relativeHumidity-6.83783*(Math.pow(10, -3))*(Math.pow($airTemperature, 2))-5.481717*(Math.pow(10, -2))*(Math.pow($relativeHumidity, 2))+1.22874*(Math.pow(10, -3))*(Math.pow($airTemperature, 2))*$relativeHumidity+8.5282*(Math.pow(10, -4))*$airTemperature*(Math.pow($relativeHumidity, 2))-1.99*(Math.pow(10, -6))*(Math.pow($airTemperature, 2))*(Math.pow($relativeHumidity,2));
            
            if (($relativeHumidity <= 13) && ($airTemperature >= 80.0) && ($airTemperature <= 112.0)) {
                $adj1 = (13.0-$relativeHumidity)/4.0;
                $adj2 = Math.sqrt((17.0-Math.abs($airTemperature-95.0))/17.0);
                $adj = $adj1 * $adj2;
                $hi = $hi - $adj;
            }
            else if (($relativeHumidity > 85.0) && ($airTemperature >= 80.0) && ($airTemperature <= 87.0)) {
                $adj1 = ($relativeHumidity-85.0)/10.0;
                $adj2 = (87.0-$airTemperature)/5.0;
                $adj = $adj1 * $adj2;
                $hi = $hi + $adj;
            }
        }
        else {
            $hi = $hifinal;
        }
    }
    
    return $hi;
}

?>
