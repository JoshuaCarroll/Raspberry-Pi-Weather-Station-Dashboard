<?php

// ______ Database functions _____________________________________________

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

// ------ General weather utilities ------------------------------------
function calculateMeanSeaLevelPressure($pressure, $elevation) {
    $msl = $pressure / (pow(1 - ($elevation / 44330.0), 5.255));
    return $msl;
}

$showMetricAndCelsiusMeasurements = getSetting("showMetricAndCelsiusMeasurements");
$showPressureInMillibars = getSetting("showPressureInMillibars");
$stationElevationInMeters = getSetting("stationElevationInMeters");

?>