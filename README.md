Raspberry Pi Weather Station Dashboard
======================================


This project will provide you with a graphical dashboard of the current weather observation based on the Raspberry Pi Weather Station data.

## Install prerequisites

1. Install the Apache2 package with the following command:

    `sudo apt-get install apache2 -y`

2. Install PHP5 and the PHP module for Apache

    `sudo apt-get install php5 libapache2-mod-php5 -y`

3. Install the MySQL DLL's for PHP5 

    `sudo apt-get install php5-mysql -y`

## Get the data logging code

1. You will need root access on the Raspberry Pi. From the command line type:

    `sudo -s`

2. Navigate to the web folder:

    `cd /var/www/html`

3. Download the files to a folder named `dashboard`:

    `git clone https://github.com/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard.git dashboard`
  
4. Return to the dashboard site root.

    `cd dashboard`

You should now be in `/var/www/html/dashboard`

## Set up and connect
  
1. Update the the php script with the MySQL password that you chose when installing the database.

    `nano variables.php`
  
    Update the variables to the values that you chose.
  
    Press `Ctrl O` then `Enter` to save and `Ctrl X` to quit nano.
    
2. Create the GETCURRENTOBS stored procedure.

    `mysql weather < GETCURRENT.sql`

3. Find the weather station's ip address:

    `ifconfig`
  
  The IP address will be on the second line just after `inet addr:`

Enter this IP address into a browser followed by `/dashboard`. For example:

  - `http://192.168.0.X/dashboard`
