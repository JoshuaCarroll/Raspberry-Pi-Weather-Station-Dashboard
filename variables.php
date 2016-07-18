<?php

// ___ Database connection settings ____________________________________
$databaseAddress = '127.0.0.1';
$databaseUsername = 'root';
$databasePassword = 'tiger';
$databaseSchema = 'weather';

/* ____________________________________________________________________________
*  
*  It is suggested that you use the SETUP.sh script to change your preferences.
*  See the README file for instructions on using the SETUP script.
*
*  Do not edit code below unless you understad the implications
------------------------------------------------------------------------------ */
set -- $(mysql -u root -p -N "select showMetricAndCelsiusMeasurements, showPressureInMillibars from RPiWx_SETTINGS")

// If false displays values in Imperial units and Fahrenheit
$showMetricAndCelsiusMeasurements = "$1" 

// If false displays pressure in inches (of Mercury)
$showPressureInMillibars = "$1"

?>