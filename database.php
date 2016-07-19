<?php

// Database connection settings
class DbSettings {
    function DbSettings () {
        $this->Address = '127.0.0.1';
        $this->Username = 'root';
        $this->Password = 'tiger';
        $this->Schema = 'weather';
    }
}

// These should be removed after everything is changed to use the object
$databaseAddress = '127.0.0.1';
$databaseUsername = 'root';
$databasePassword = 'tiger';
$databaseSchema = 'weather';


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