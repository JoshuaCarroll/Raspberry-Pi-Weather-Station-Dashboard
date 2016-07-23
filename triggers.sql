# This is not automatically installed. I added this to my own installation, and shared it in case someone else wanted it.
# If you run this script, your station will not record readings if any of them are -1000. This happens when your station
# can't get a reading for one reason or another. I intend to add a new table where errors like this will be logged, but
# for now they just get dropped.

USE `weather`;
DELIMITER $$
CREATE TRIGGER `wx_beforeInsert` BEFORE INSERT ON WEATHER_MEASUREMENT 
FOR EACH ROW BEGIN
	IF (NEW.AMBIENT_TEMPERATURE = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Ambient tempurature invalid. Record rejected.";
	ELSEIF (NEW.GROUND_TEMPERATURE = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Ground tempurature invalid. Record rejected.";
	ELSEIF (NEW.AIR_QUALITY = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Air quality invalid. Record rejected.";
	ELSEIF (NEW.AIR_PRESSURE = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Air pressure invalid. Record rejected.";
	ELSEIF (NEW.HUMIDITY = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Humidity invalid. Record rejected.";
	END IF;
END;
$$

DELIMITER &&
CREATE TRIGGER `wx_beforeUpdate` BEFORE UPDATE ON WEATHER_MEASUREMENT 
FOR EACH ROW BEGIN
	IF (NEW.AMBIENT_TEMPERATURE = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Ambient tempurature invalid. Record rejected.";
	ELSEIF (NEW.GROUND_TEMPERATURE = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Ground tempurature invalid. Record rejected.";
	ELSEIF (NEW.AIR_QUALITY = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Air quality invalid. Record rejected.";
	ELSEIF (NEW.AIR_PRESSURE = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Air pressure invalid. Record rejected.";
	ELSEIF (NEW.HUMIDITY = '-1000') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = "Humidity invalid. Record rejected.";
	END IF;
END;
&&