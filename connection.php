<?php

/**
 * File containing the database connection.
 */

$dbHost = 'localhost';
$dbName = 'phpepignosis';
$dbUser = 'root';
$dbPass = '';

try
{
    $database = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $exception)
{
    echo $exception->getMessage();
}