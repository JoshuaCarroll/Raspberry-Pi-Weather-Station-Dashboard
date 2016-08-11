#!/bin/bash

CurrentUser="$(whoami)"
if [ "$CurrentUser" = "root" ]
then
  echo "$CurrentUser ok."
else
  echo "Usage: sudo ./setup.sh";
  exit 1
fi


####################################################################
#
# Function definitions
#
####################################################################
getBreadcrumbs() {
    local currentSection="$1"
    getBreadcrumbs_=""
    sections=('intro' 'database' 'preferences' 'weather_underground' 'dynamic_dns' 'done')
    local firstOne=1
    for i in ${sections[@]}; do
        j=$i
        if [ $currentSection = $j ]
        then
            j="${j^^}"
        fi
        if [ $firstOne -eq 0 ] 
        then
            getBreadcrumbs_="$getBreadcrumbs_  /  "
        fi
        getBreadcrumbs_="$getBreadcrumbs_$j"
        firstOne=0
    done
    
}



####################################################################
#
# Introduction
#
####################################################################
clear
echo 
echo 
echo 
getBreadcrumbs "intro"
echo $getBreadcrumbs_
echo
echo
echo
echo "           _\|/_"
echo "          (o o)"
echo "  +----oOO-{_}-OOo----------------------+"
echo "  |                                     |"
echo "  |    Raspberry Pi Weather Station     |"
echo "  |      Dashboard Setup Utility        |"
echo "  |                                     |"
echo "  |                                     |"
echo "  | Created by Joshua Carroll           |"
echo "  | Released under the MIT license      |"
echo "  +-------------------------------------+"
echo
echo
echo "  This small shell script will help you configure your weather station. Simply answer the"
echo "  questions, and it will take care of the hard work.  If you need to change your selections in"
echo "  the future, simply run this utility again."
echo
echo "  As you answer the questions in this utility, some will have default answers in brackets [].  If"
echo "  you want to use the default answer, simply press <ENTER> for that question."
echo 
echo "  If you find any bugs or have any suggestions, please open an issue in the Github repository at:"
echo "    https://github.com/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard"
echo
echo
echo "  Press <ENTER> when you are ready to begin..."
read enter



####################################################################
#
# Database setup
#
####################################################################
clear
echo 
echo 
echo 
getBreadcrumbs "database"
echo $getBreadcrumbs_
echo "  This program will make several calls to the SETTINGS table in your database. To do this, you"
echo "  will need to provide your database connection information."

echo 
orgDatabaseAddress="$(grep -oE '\$Address = .*;' database.php | tail -1 | sed 's/$Address = //g;s/;//g')"
if [ -z "$orgDatabaseAddress" ]
then
  orgDatabaseAddress="127.0.0.1"
fi
echo -n "Database server address [$orgDatabaseAddress]: "
read databaseAddress
if [ -z "$databaseAddress" ]
then
  databaseAddress="$orgDatabaseAddress"
fi

echo 
orgDatabaseSchema="$(grep -oE '\$Schema = .*;' database.php | tail -1 | sed 's/$Schema = //g;s/;//g')"
if [ -z "$orgDatabaseSchema" ]
then
  orgDatabaseSchema="weather"
fi
echo -n "Database schema [$orgDatabaseSchema]: "
read databaseSchema
if [ -z "$databaseSchema" ]
then
  databaseSchema="$orgDatabaseSchema"
fi

echo
orgDatabaseUsername="$(grep -oE '\$Username = .*;' database.php | tail -1 | sed 's/$Username = //g;s/;//g')"
if [ -z "$orgDatabaseUsername" ]
then
  orgDatabaseUsername="root"
fi
echo -n "Database username [$orgDatabaseUsername]: "
read databaseUsername
if [ -z "$databaseUsername" ]
then
  databaseUsername="$orgDatabaseUsername"
fi

echo
orgDatabasePassword="$(grep -oE '\$Password = .*;' database.php | tail -1 | sed 's/$Password = //g;s/;//g')"
if [ -z "$orgDatabasePassword" ]
then
  orgDatabasePassword="root"
fi
echo -n "Database password [$orgDatabasePassword]: "
read databasePassword
if [ -z "$databasePassword" ]
then
  databasePassword="$orgDatabasePassword"
fi

echo
echo
echo "Creating the database.php file with these options..."
echo
echo "<?php" > "database.php"
echo "include 'utilities.php';" >> "database.php"
echo "" >> "database.php"
echo "// Database connection settings" >> "database.php"
echo "class DbSettings {" >> "database.php"
echo "    public static $Address = '$databaseAddress';" >> "database.php"
echo "    public static $Username = '$databaseUsername';" >> "database.php"
echo "    public static $Password = '$databasePassword';" >> "database.php"
echo "    public static $Schema = '$databaseSchema';" >> "database.php"
echo "}" >> "database.php"
echo "" >> "database.php"
echo "?>" >> "database.php"

echo 
echo 
echo "Executing: mysql -vv -h $databaseAddress -u'$databaseUsername' -p'$databasePassword' -e 'CREATE DATABASE IF NOT EXISTS $databaseSchema'"
mysql -vv -h "$databaseAddress" -u "$databaseUsername" -p"$databasePassword" -e "CREATE DATABASE IF NOT EXISTS $databaseSchema"

echo
echo
echo "  Next, we will install (or update) stored procedures and create the table for settings to be stored."
echo "  NOTE: Even if you have done this before it may be a good idea to run it again, especially if you have pulled a new update from the repository."
echo -n "  Continue?  [Y/n]: "
read runSetupSql
if [ "$runSetupSql" = "y" ] || [ "$runSetupSql" = "Y" ] || [ "$runSetupSql" = "" ]
then
  echo 
  echo 
  echo "    Executing SETUP.sql."
  echo "---------------------------------------------------------------------------------------------"
  mysql -vv -e -h "$databaseAddress" -u "$databaseUsername" -p"$databasePassword" "$databaseSchema" < setup.sql
  echo "---------------------------------------------------------------------------------------------"
  echo 
  echo "  Press <ENTER> to continue..."
  read enter
fi
echo 
echo 
echo 



####################################################################
#
# Dashboard preferences
#
####################################################################
clear
echo 
echo 
echo 
getBreadcrumbs "preferences"
echo $getBreadcrumbs_
echo
echo
echo
echo "  Your measurements will be recorded in the database in Celsius and metric units. But how do you"
echo "  want the dashboard to display the measurements?"
echo "    (0) Fahrenheit and imperial units"
echo "    (1) Celsius and metric units"
echo
echo -n "  !> "
read showMetricAndCelsiusMeasurements
if [ "$showMetricAndCelsiusMeasurements" = "0" ] || [ "$showMetricAndCelsiusMeasurements" = "1" ]
then
    echo 
    echo 
    echo "    Storing setting showMetricAndCelsiusMeasurements = $showMetricAndCelsiusMeasurements."
    echo "---------------------------------------------------------------------------------------------"
    mysql -vv -u root -p"$databasePassword" weather -e "Update RPiWx_SETTINGS set value='$showMetricAndCelsiusMeasurements' where name='showMetricAndCelsiusMeasurements'"
    echo "---------------------------------------------------------------------------------------------"
else
    echo "    Invalid selection.  Moving on..."
fi
echo 
echo 
echo "  Press <ENTER> to continue..."
read enter

clear
echo 
echo 
echo 
getBreadcrumbs "preferences"
echo $getBreadcrumbs_
echo
echo
echo
echo "  Barometric pressure will be recorded in millibars. How do you want the dashboard to display the"
echo "  barometric pressure?"
echo "    (0) Inches of mercury"
echo "    (1) Millibars"
echo 
echo -n "  !> "
read showPressureInMillibars
if [ "$showPressureInMillibars" = "0" ] || [ "$showPressureInMillibars" = "1" ]
then
    echo 
    echo 
    echo "    Storing setting showPressureInMillibars = $showPressureInMillibars."
    echo "---------------------------------------------------------------------------------------------"
    mysql -vv -u root -p"$databasePassword" weather -e "Update RPiWx_SETTINGS set value='$showPressureInMillibars' where name='showPressureInMillibars'"
    echo "---------------------------------------------------------------------------------------------"
else
    echo "    Invalid selection.  Moving on..."
fi
echo
echo
echo "  Press <ENTER> to continue..."
read enter

clear
echo 
echo 
echo 
getBreadcrumbs "preferences"
echo $getBreadcrumbs_
echo
echo
echo
echo "  Barometric pressure is usually shown relative to Mean Sea Level. In order to calculate this"
echo "  the program needs the elevation of the station from sea level in meters.  If you don't know"
echo "  your elevation, you can find it at http://www.whatismyelevation.com"
echo 
echo "  'station elevation' = 'ground elevation' + 'meters above the ground'"
echo 
echo 
echo -n "  What is your station elevation in meters? "
read stationElevationInMeters
echo 
echo 
echo "    Storing setting stationElevationInMeters = $stationElevationInMeters."
echo "---------------------------------------------------------------------------------------------"
mysql -vv -u root -p"$databasePassword" weather -e "call UPDATEWXSETTING('stationElevationInMeters','$stationElevationInMeters')"
echo "---------------------------------------------------------------------------------------------"
echo
echo
echo "  Press <ENTER> to continue..."
read enter

clear
echo 
echo 
echo 
getBreadcrumbs "preferences"
echo $getBreadcrumbs_
echo
echo
echo
echo "  Do you have a soil temperature probe installed?"
echo "    (0) No"
echo "    (1) Yes"
echo
echo -n "  !> "
read soilTemperatureProbePresent
if [ "$soilTemperatureProbePresent" = "0" ] || [ "$soilTemperatureProbePresent" = "1" ]
then
    echo 
    echo 
    echo "    Storing setting soilTemperatureProbePresent = $soilTemperatureProbePresent."
    echo "---------------------------------------------------------------------------------------------"
    mysql -vv -u root -p"$databasePassword" weather -e "call UPDATEWXSETTING('soilTemperatureProbePresent','$soilTemperatureProbePresent')"
    echo "---------------------------------------------------------------------------------------------"
else
    echo "    Invalid selection.  Moving on..."
fi
echo
echo
echo "  Press <ENTER> to continue..."
read enter



####################################################################
#
# Weather Underground setup
#
####################################################################
clear
echo 
echo 
echo 
getBreadcrumbs "weather_underground"
echo $getBreadcrumbs_
echo
echo
echo
echo -n "  Do you want to setup your station to report readings to Weather Underground? (y/N): "
read reportToWunderground
echo
echo

if [ "$reportToWunderground" = "y" ] || [ "$reportToWunderground" = "Y" ] 
then
  echo -n "  Do you already have a station ID and key? (Y/n): "
  read haveStationID
  echo
  echo

  if [ "$haveStationID" = "n" ] || [ "$haveStationID" = "N" ]
  then
    echo "    ****************************************************************************************"
    echo "    *                                                                                      *"
    echo "    *  Go online and request a station ID and key. This process will take about 1 minute.  *"
    echo "    *  Once you are done come back here and press <ENTER>.                                 *"
    echo "    *                                                                                      *"
    echo "    *  To setup your station and get a station ID and key, browse to:                      *"
    echo "    *          https://www.wunderground.com/personal-weather-station/signup                *"
    echo "    *                                                                                      *"
    echo "    ****************************************************************************************"
    echo
    echo "    Press <ENTER> to continue..."
    echo
    echo
    read enter
    echo
    echo
  fi

  echo -n "  What is your Weather Underground station ID? "
  read wuStationID
  mysql -u root -p"$databasePassword" weather -e "Update RPiWx_SETTINGS set value='$wuStationID' where name='WUNDERGROUND_ID'"
  echo
  echo -n "  What is your Weather Underground station key? "
  read wuStationKey
  mysql -u root -p"$databasePassword" weather -e "Update RPiWx_SETTINGS set value='$wuStationKey' where name='WUNDERGROUND_PASSWORD'"
  echo
  echo -n "  At what minute interval would you like your system to send data to Weather Underground [10]? "
  read wuMinutes
  echo 
  echo -n "  What is the localhost URL to the wunderground-api.php [http://localhost/dashboard/wunderground-api.php]? "
  read wuURL
  echo 
  
  # Write out current crontab
  crontab -l > ~/crontemp1
  # If there is a line already presnt for Weather Underground, remove it.
  grep -vwE "wunderground-api.php" ~/crontemp1 > ~/crontemp2
  # New cron into cron file
  echo "*/$wuMinutes * * * * curl $wuURL" >> ~/crontemp2
  # Install new cron file
  crontab ~/crontemp2
  # Remove temp files
  rm ~/crontemp1
  rm ~/crontemp2
  echo 
  echo
  echo "    Weather Underground CRON job created and will run every $wuMinutes minutes."
  echo
  echo 
  echo "  Press <ENTER> to continue..."
  read enter
fi


####################################################################
#
# Dynamic DNS setup
#
####################################################################
clear
echo 
echo 
echo 
getBreadcrumbs "dynamic_dns"
echo $getBreadcrumbs_
echo
echo
echo 
echo -n "  Would you like information about setting up dynamic DNS so you can access your weather dashboard remotely? [y/N]: "
read dynamicDnsInfo
if [ $dynamicDnsInfo = "y" ] || [ $dynamicDnsInfo = "Y" ]
then
    echo 
    echo 
    echo 
    echo "  Dynamic DNS is the term the describes a service that will provide DNS services that can be rapidly"
    echo "  updated for changing IP addresses. Most residential internet connections have dynamic IP addresses, "
    echo "  meaning the IP address can and will change without notice."
    echo 
    echo "  Those who would like to host their own website must either pay business rates for Internet access"
    echo "  (and a static IP address), or they must use a dynamic DNS service. There are many available, but"
    echo "  I use DuckDNS.org: a free and simple dynamic DNS service."
    echo 
    echo "  The process of setting this up is very simple:"
    echo 
    echo "    1. Just browse to https://www.duckdns.org"
    echo "    2. Create an account (relax, it's free)."
    echo "    3. Select and add a domain to your account."
    echo "    4. In the main menu, click 'install'."
    echo "    5. Select your operating system (probably 'linux cron'), then select the domain you just created"
    echo "       in the dropdown list."
    echo "    6. Follow the instructions provided. These are specific to your setup, account, and domain - so"
    echo "       just enter the commands given exactly."
    echo 
    echo "  At this point you'll need to refer to your router's instructions for allowing HTTP traffic (port 80)"
    echo "  through to your Raspberry Pi."
    echo 
fi



####################################################################
#
# Done
#
####################################################################
clear
echo 
echo 
echo 
getBreadcrumbs "done"
echo $getBreadcrumbs_
echo
echo
echo
echo "   Done.  Remember, if you find any bugs or have any suggestions, please open an issue in the Github repository at:"
echo "    https://github.com/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard"
echo 
echo 
echo 
echo 
echo 
echo 
echo 
echo 

