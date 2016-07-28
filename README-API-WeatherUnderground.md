Weather Underground API integration
===================================

The easiest way to setup Weather Underground integration, is to simply run the `setup.sh` script.  But if you are a control freak, here are the steps to do it yourself.

1. Run the SQL statement again. This is written in such a way that it will only add whatever you don't have.

    `mysql -u root -p weather < SETUP.sql`

2. Add your own values to the settings table.

    ```
    mysql -u root -p weather -e "update RPiWx_SETTINGS set value='YOUR-STATION-ID' where name='WUNDERGROUND_ID'"
    mysql -u root -p weather -e "update RPiWx_SETTINGS set value='YOUR-STATION-PASSWORD' where name='WUNDERGROUND_PASSWORD'"
    ```
    
3. Open CRONTAB to configure a recurring task.

    `crontab -e`
    
4. Set up a CRON job to automatically send data to Weather Underground every 10 minutes. (Change the URL to match your environment.)

    `*/10 * * * * curl http://localhost/dashboard/wunderground-api.php`
    
5. Press `Ctrl O` then `Enter` to save and `Ctrl X` to quit nano.