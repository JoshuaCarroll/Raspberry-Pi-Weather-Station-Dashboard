<?php

function getSetting($setting) {
    $con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);
    $returnValue = "";
    
    if (mysqli_connect_errno()) {
        $returnValue = "ERROR CONNECTING TO DATABASE";
    }
    else {
        $result = $con->query("select VALUE from RPiWx_SETTINGS where name = '$setting' ");
        if( !$result ) {
          $returnValue = "";
        }
        else {
          $returnValue = $result->fetch_object()->VALUE;
        }
    }
    
    return $returnValue;
}

function calculateMeanSeaLevelPressure($pressure, $elevation) {
    $msl = $pressure / (pow(1 - ($elevation / 44330.0), 5.255));
    return $msl;
}

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

function convertmillimetersToInches($mm) {
    $inches = $mm * 0.039370;
    return $inches;
}

function calculateDewPointF($tempC, $humidity) {
    $dewPoint = $tempC - ((100 - $humidity) / 5);
    $dewPoint = convertCelsiusToFahrenheit($dewPoint);
    return $dewPoint;
}

// Database connection settings
class Settings {
    public static $showMetricAndCelsiusMeasurements = getSetting("showMetricAndCelsiusMeasurements");
    public static $showPressureInMillibars = getSetting("showPressureInMillibars");
    public static $stationElevationInMeters = getSetting("stationElevationInMeters");
}


?>