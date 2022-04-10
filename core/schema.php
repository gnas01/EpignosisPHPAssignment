<?php

namespace core;

require_once 'customRule.php';

/**
 *  Base class for schemas that are used to validate data.
 */
abstract class Schema
{
    /** Any validation errors will be stored here */
    private array $errors = [];

    /** Custom rules in the forms of callback functions*/
    private array $customRules = [];

    /**
     *  Loads data coming from a request into the schema.
     *  @param array $data The data to be loaded.
     */
    public function loadData($data): void
    {
        foreach ($data as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    /** Returns any validation errors
     * @return array The validation errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /** Crafts a custom rule based on a bool callback function
     * @param callable $callback The callback function that will be used to validate the data,
     * must return true to pass validation.
     * @param string $message The error message to be displayed if the rule fails.
     */
    protected function addCustomRule($callback, string $message): void
    {
        array_push($this->customRules, new CustomRule($message, $callback));
    }

    /** Function that will contain all validation rules, meant to be
     * overrided by the child class.
     */
    abstract public function rules(): array;

    /**
     *  Validates the data using the rules defined in the child class.
     *  Contains several custom rules that can be used to validate data.
     *  @return bool True if the data is valid, false otherwise.
     */
    public function isValid() : bool
    {
        foreach ($this->rules() as $key => $value)
        {
            if (isset($value['required']) && $value['required'] && !isset($this->{$key}))
            {
                array_push($this->errors, "The field $key is required");
            }
            if (isset($value['min']) && isset($this->{$key}) && strlen($this->{$key}) < $value['min'])
            {
                array_push($this->errors, "The field $key must be at least {$value['min']} characters long");
            }
            if (isset($value['max']) && isset($this->{$key}) && strlen($this->{$key}) > $value['max'])
            {
                array_push($this->errors, "The field $key must be less than {$value['max']} characters long");
            }
            if (isset($value['email']) && isset($this->{$key}) && !filter_var($this->{$key}, FILTER_VALIDATE_EMAIL))
            {
                array_push($this->errors, "The field $key must be a valid email address");
            }
            if (isset($value['match']) && isset($this->{$key}) && $this->{$key} !== $this->{$value['match']})
            {
                array_push($this->errors, "The field $key must match the field {$value['match']}");
            }
            if (isset($value['integer']) && isset($this->{$key}) && !is_numeric($this->{$key}))
            {
                array_push($this->errors, "The field $key must be an integer");
            }
            if (isset($value['date']) && isset($this->{$key}) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->{$key}))
            {
                array_push($this->errors, "The field $key must be in the format YYYY-MM-DD");
            }
        }

        /**
         * Iterates the customRules array, and if any of them fail,
         * pushes the error message to the errors array.
         */

        foreach ($this->customRules as $customRule)
        {
            if(!$customRule->getCallback())
            {
                array_push($this->errors, $customRule->getError());
                return false;
            }
        }
        return empty($this->errors);
    }
}

?>