<?php

namespace core;
use PDO;
use PDOException;

/**
 * File containing the database connection.
 */

class Database
{
    private const HOST = "localhost";
    private const DB_NAME = "phpepignosis";
    private const DB_USER = "root";
    private const DB_PASS = "";
    private static $conn = null;

    /**
     * Initalizes database connection and stores it in a static variable.
     *
     * @return void
     */
    private static function initConnection()
    {
        try
        {
            self::$conn = new PDO("mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME, self::DB_USER, self::DB_PASS);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $exception)
        {
            echo "Connection failed: " . $exception->getMessage();
        }
    }
    
    /**
     * Retrieves the database connection.
     * If null, calls initConnection() to create a new connection.
     *
     * @return mixed
     */
    public static function getConnection()
    {
        if(self::$conn == null)
        {
            self::initConnection();
        }
        
        return self::$conn;
    }
}