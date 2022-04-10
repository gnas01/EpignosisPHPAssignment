<?php

/**
 * Helper class used for better handling
 * session variables. 
 */
class SessionEditor
{
    public const ALERTS = "alerts";
    public const SELECTED_USER = "selected_user";
    public const USER = "user";
    public const AUTHENTICATED  = "authenticated";

    /**
     * Returns unserialized object based on the key provided.
     *
     * @param string $attribute The key of the session variable.
     * @return object|null The object stored in the session variable.
     */
    public static function getObject(string $attribute) : ?object
    {
        if(isset($_SESSION[$attribute]))
        {
            return unserialize($_SESSION[$attribute]);
        }

        return null;
    }
    
    /**
     * Sets serialized object in the session variable.
     *
     * @param string $attribute The key of the session variable.
     * @param object $object The object to be stored in the session variable.
     * @return void
     */
    public static function setObject(string $attribute, object $object): void
    {
        $_SESSION[$attribute] = serialize($object);
    }

    /**
     * Returns the value of the session variable.
     *
     * @param string $attribute The key of the session variable.
     * @return mixed|null The value of the session variable.
     */
    public static function getAttribute(string $attribute)
    {
        if(isset($_SESSION[$attribute]))
        {
            return $_SESSION[$attribute];
        }

        return null;
    }
    
    /**
     * Sets the value of the session variable.
     *
     * @param string $attribute The key of the session variable.
     * @param mixed $value The value of the session variable.
     * @return void
     */
    public static function setAttribute(string $attribute, $value): void
    {
        $_SESSION[$attribute] = $value;
    }

    /**
     * Initalizes an array in the session variable, where
     * values can be pushed.
     * 
     * @param string $attribute The key of the session array.
     * @param mixed $value The value to be pushed into the array.
     */
    public static function addAttribute(string $attribute, $value): void
    {
        if(!isset($_SESSION[$attribute]))
        {
            $_SESSION[$attribute] = [];
        }

        array_push($_SESSION[$attribute], $value);
    }

    /**
     * Removes the session variable.
     *
     * @param string $attribute The key of the session variable.
     * @return void
     */
    public static function removeAttribute(string $attribute)
    {
        unset($_SESSION[$attribute]);
    }
    
    /** Destroys session */
    public static function clear()
    {
        session_unset();
        session_destroy();
    }
   
    /** Initializes session */
    public static function start()
    {
        session_start();
    }
}

?>