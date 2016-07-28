# This is not automatically installed. I added this to my own installation, and shared it in case someone else wanted it.
# If you run this script, your station will not record readings if any of them are -1000. This happens when your station
# can't get a reading for one reason or another. I intend to add a new table where errors like this will be logged, but
# for now they just get dropped.

USE `weather`;

-- Create the WEATHER_MEASUREMENT_ERRORS table if it doesn't exist
CREATE TABLE IF NOT EXISTS `WEATHER_MEASUREMENT_ERRORS` (
    `ID` bigint(20) NOT NULL AUTO_INCREMENT,
    `REMOTE_ID` bigint(20) DEFAULT NULL,
    `AMBIENT_TEMPERATURE` decimal(6,2) NOT NULL,
    `GROUND_TEMPERATURE` decimal(6,2) NOT NULL,
    `AIR_QUALITY` decimal(6,2) NOT NULL,
    `AIR_PRESSURE` decimal(6,2) NOT NULL,
    `HUMIDITY` decimal(6,2) NOT NULL,
    `WIND_DIRECTION` decimal(6,2) DEFAULT NULL,
    `WIND_SPEED` decimal(6,2) NOT NULL,
    `WIND_GUST_SPEED` decimal(6,2) NOT NULL,
    `RAINFALL` decimal(6,4) DEFAULT NULL,
    `CREATED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ERROR_MESSAGE` varchar(50) NOT NULL,
    PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=4126 DEFAULT CHARSET=latin1;


DROP TRIGGER IF EXISTS wx_beforeInsert;

DELIMITER $$
CREATE TRIGGER `wx_beforeInsert` BEFORE INSERT ON WEATHER_MEASUREMENT 
FOR EACH ROW BEGIN
	SET @ERROR_MESSAGE = '';

	IF (NEW.AMBIENT_TEMPERATURE = '-1000') THEN
		SET @ERROR_MESSAGE = "Ambient tempurature invalid. Record rejected.";
	ELSEIF (NEW.GROUND_TEMPERATURE = '-1000') THEN
        SET @ERROR_MESSAGE = "Ground tempurature invalid. Record rejected.";
	ELSEIF (NEW.AIR_QUALITY = '-1000') THEN
        SET @ERROR_MESSAGE = "Air quality invalid. Record rejected.";
	ELSEIF (NEW.AIR_PRESSURE = '-1000') THEN
        SET @ERROR_MESSAGE = "Air pressure invalid. Record rejected.";
	ELSEIF (NEW.HUMIDITY = '-1000') THEN
        SET @ERROR_MESSAGE = "Humidity invalid. Record rejected.";
	END IF;
    
    IF (@ERROR_MESSAGE <> '') THEN
		INSERT INTO `weather`.`WEATHER_MEASUREMENT_ERRORS`
		(`AMBIENT_TEMPERATURE`,
		`GROUND_TEMPERATURE`,
		`AIR_QUALITY`,
		`AIR_PRESSURE`,
		`HUMIDITY`,
		`WIND_DIRECTION`,
		`WIND_SPEED`,
		`WIND_GUST_SPEED`,
		`RAINFALL`,
		`CREATED`,
        `ERROR_MESSAGE`)
		VALUES
		(NEW.AMBIENT_TEMPERATURE,
		NEW.GROUND_TEMPERATURE,
		NEW.AIR_QUALITY,
		NEW.AIR_PRESSURE,
		NEW.HUMIDITY,
		NEW.WIND_DIRECTION,
		NEW.WIND_SPEED,
		NEW.WIND_GUST_SPEED,
		NEW.RAINFALL,
		NEW.CREATED,
        @ERROR_MESSAGE);
    
		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = @ERROR_MESSAGE;
	END IF;
END;
$$