Weather Underground API integration
===================================

1. Run the SQL statement again. This is written in such a way that it will only add whatever you don't have.

    `mysql -u root -p weather < CREATE-SP.sql`

2. Add your own values to the settings table.

    ```
    mysql -u root -p weather -e "update SETTINGS set value='YOUR-STATION-ID' where name='WUNDERGROUND_ID'"
    mysql -u root -p weather -e "update SETTINGS set value='YOUR-STATION-PASSWORD' where name='WUNDERGROUND_PASSWORD'"
    ```
    
3. Open CRONTAB to configure a recurring task.

    `crontab -e`
    
4. Set up a CRON job to automatically send data to Weather Underground every 10 minutes. (Change the URL to match your environment.)

    `*/10 * * * * curl http://localhost/dashboard/wunderground-api.php`
    
5. Press `Ctrl O` then `Enter` to save and `Ctrl X` to quit nano.