<?php
include 'utilities.php';

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
$globalDbSettings = new DbSettings();

$databaseAddress = $globalDbSettings->Address;
$databaseUsername = $globalDbSettings->Username;
$databasePassword = $globalDbSettings->Password;
$databaseSchema = $globalDbSettings->Schema;

?>