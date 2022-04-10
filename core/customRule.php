<?php

namespace core;
/**
 * Helper class for the schema class.
 * Contains a custom rule that can be used to validate data.
 */
class CustomRule
{
    /**
     * The error message to be displayed if the rule fails.
     */
    private string $error; 
    /** The custom rule logic in a callback function
     * must true to pass validation.
     */
    private $callback;

    public function __construct(string $error, $callback)
    {
        $this->error = $error;
        $this->callback = $callback;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getCallback() 
    {
        return $this->callback;
    }
}
?>