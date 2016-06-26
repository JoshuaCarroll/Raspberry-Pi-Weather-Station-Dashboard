<?php
include 'variables.php';
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
$con=mysql_connect($databaseAddress,$databaseUsername,$databasePassword);
mysql_select_db('weather', $con);

$select_query = "CALL GETRECENTOBS";

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysql_query($select_query) or die(mysql_error());

echo "{ \"WeatherObservation\": { ";
$numberOfFields = mysql_num_fields($result);
$numberOfRows = 0;

while($row = mysql_fetch_array($result)) {
    $numberOfRows++;

    if ($numberOfRows > 1) {
        echo ", ";
    }

    echo "\"Observation" . $numberOfRows . "\": { ";
    for($i = 0; $i < $numberOfFields; $i++) {
        $field_info = mysql_fetch_field($result, $i);
        $fieldName = $field_info->name;
        $fieldValue = $row[$i];
        
        if (($fieldName ==  "AMBIENT_TEMPERATURE") || ($fieldName =="GROUND_TEMPERATURE")) {
            if ($useMetricAndCelsiusMeasurements) {
                echo "\"" . $fieldName . "_STRING\":";
                echo "\"" . $fieldValue . "° C\"";
            } else {
                $fieldValue = convertCelsiusToF($fieldValue);
                
                echo "\"" . $fieldName . "_STRING\":";
                echo "\"" . $fieldValue . "° F\"";
            }
        }
        
        echo "\"" . $fieldName . "\":";
        echo "\"" . $fieldValue . "\"";

        if ($i+1 < $numberOfFields) {
                echo ",";
        }
    }
    echo " } ";
}

echo "} }";

mysql_close($con);

function convertCelsiusToF($celsiusDegrees) {
    $F = ((($celsiusDegrees * 9) / 5) + 32);
    return $F;
}
?>