<?php

// ______ Database functions _____________________________________________

function getSetting($setting) {
    $dbSettings = new DbSettings();
    $con = new mysqli($dbSettings->Address,$dbSettings->Username,$dbSettings->Password,$dbSettings->Schema);
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

$showMetricAndCelsiusMeasurements = getSetting("showMetricAndCelsiusMeasurements");
$showPressureInMillibars = getSetting("showPressureInMillibars");

?>