<?php

class SessionEditor
{
    public const ALERTS = "alerts";
    public const SELECTED_USER = "selected_user";
    public const USER = "user";
    public const AUTHENTICATED  = "authenticated";

    public static function getAttribute(string $attribute)
    {
        if(isset($_SESSION[$attribute]))
        {
            return $_SESSION[$attribute];
        }

        return null;
    }

    public static function setAttribute(string $attribute, $value)
    {
        $_SESSION[$attribute] = $value;
    }

    public static function addAttribute(string $attribute, $value)
    {
        if(!isset($_SESSION[$attribute]))
        {
            $_SESSION[$attribute] = [];
        }

        array_push($_SESSION[$attribute], $value);
    }

    public static function removeAttribute(string $attribute)
    {
        unset($_SESSION[$attribute]);
    }
    
    public static function clear()
    {
        session_unset();
        session_destroy();
    }
   
    public static function start()
    {
        /*including files that are needed for the
         session to automatically serialize/deserialize*/

        include_once "./models/userModel.php";
        session_start();
    }
}

?>