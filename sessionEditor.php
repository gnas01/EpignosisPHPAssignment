<?php

class SessionEditor
{
    public const ALERTS = "alerts";

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
}

?>