<?php

function getSetting($setting) {
    $returnValue = "";
    
    //if (apc_exists($setting)) {
    //    $returnValue = apc_fetch($setting);
    //} else {
        $con = new mysqli(DbSettings::$Address,DbSettings::$Username,DbSettings::$Password,DbSettings::$Schema);

        if (mysqli_connect_errno()) {
            $returnValue = "ERROR";
        }
        else {
            $result = $con->query("select VALUE from RPiWx_SETTINGS where name = '$setting' ");
            if( !$result ) {
              $returnValue = "";
            }
            else {
              $returnValue = $result->fetch_object()->VALUE;
            }
    //        apc_store($setting, $returnValue);
        }
    //}
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

function convertFahrenheitToCelsius($fahrenheitDegrees) {
    $C = ($fahrenheitDegrees - 32) * 5 / 9;
    return $C;
}

function convertMillibarsToInches($millibars) {
    $inches = $millibars * 0.0295301;
    return $inches;
}

function convertMillimetersToInches($mm) {
    $inches = $mm * 0.039370;
    return $inches;
}

function calculateDewPointF($tempC, $humidity) {
    $dewPoint = $tempC - ((100 - $humidity) / 5);
    $dewPoint = convertCelsiusToFahrenheit($dewPoint);
    return $dewPoint;
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

// Database connection settings
class Settings {
    public static $showMetricAndCelsiusMeasurements = NULL;
    public static $showPressureInMillibars = NULL;
    public static $stationElevationInMeters = NULL;
    
    public function __construct() {
        if ( (!isset(self::$showMetricAndCelsiusMeasurements)) || (!isset(self::$showPressureInMillibars)) || (!isset(self::$stationElevationInMeters)) )  {
            self::initializeStStateArr();
        }
    }

    public static function initializeStStateArr() {
        if (!isset(self::$showMetricAndCelsiusMeasurements)) {
            self::$showMetricAndCelsiusMeasurements = getSetting("showMetricAndCelsiusMeasurements");
        }
        
        if (!isset(self::$showPressureInMillibars)) {
            self::$showPressureInMillibars = getSetting("showPressureInMillibars");
        }
        
        if (!isset(self::$stationElevationInMeters)) {
            self::$stationElevationInMeters = getSetting("stationElevationInMeters");
        }
    }
}
Settings::initializeStStateArr();

?>