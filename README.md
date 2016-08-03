Raspberry Pi Weather Station Dashboard
======================================

This project will provide you with a graphical dashboard of the current weather observation based on the Raspberry Pi Weather Station data.

You can see a live version of the master branch at [n5jlc.duckdns.org/dashboard](http://n5jlc.duckdns.org/dashboard).

#### Contributing

Contributions to this project can be made by using the program and providing feedback or by assisting in the programming and submitting pull requests. In either case, please read the [contributing page](https://github.com/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard/blob/master/CONTRIBUTING.md) for instructions.

#### Questions / Comments

**Please don't ask question in the issue tracker**, instead ask them in a [![chat on Gitter](https://badges.gitter.im/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard.svg)](https://gitter.im/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge).  I will be notified when something is posted in the chat room, and if I'm available I'll join you.

----------

# Installation

### Install prerequisites


1. Install the Apache2 package with the following command:

    `sudo apt-get install apache2 -y`

2. Install PHP5 and the PHP module for Apache

    `sudo apt-get install php5 libapache2-mod-php5 -y`

3. Install the MySQL DLL's for PHP5 

    `sudo apt-get install php5-mysql -y`

### Get the dashboard code

1. Navigate to the web folder:

    `cd /var/www/html`

2. Download the files to a folder named `dashboard`:

    `sudo git clone https://github.com/JoshuaCarroll/Raspberry-Pi-Weather-Station-Dashboard.git dashboard`
  
3. Return to the dashboard site root.

    `cd dashboard`

You should now be in `/var/www/html/dashboard`

### Set up and connect
  
1. Update the the php script with the MySQL password that you chose when installing the database.

    `sudo nano database.php`
  
    Update the database connection variables to the values for your environment.
  
    Press `Ctrl O` then `Enter` to save and `Ctrl X` to quit nano.

2. Run the setup script to setup additional preferences.

    `./setup.sh`

3. Find the weather station's ip address:

    `ifconfig`
  
    The IP address will be on the second line just after `inet addr:`. Enter this IP address into a browser followed by `/dashboard`. For example:

    `http://192.168.0.X/dashboard`
  
### Set up external API's (optional)

Configuration of supported external APIs is included in the setup script, but if you would like to see the details, they are included in the respective links below:

- Weather Underground: See [README-API-WeatherUnderground.md](README-API-WeatherUnderground.md).
  
----------

# Installing updates

New features and bug-fixes will be added to this repository as needed. If you want to update your copy with the latest changes, follow these steps.

1. Navigate to the dashboard folder:

    `cd /var/www/html/dashboard`
    
2. Backup your database settings file:

    `sudo cp database.php ../`
    
3. Update your code with the latest changes:

    `sudo git pull`
    
3. Restore your database settings file:

    `sudo mv ../database.php ./`
    
5. Update the stored procedure in your database:

    `sudo mysql -u root -p weather < SETUP.sql`
    
6. Run the setup script to update database objects and setup additional preferences:

    `./setup.sh`
    
    
