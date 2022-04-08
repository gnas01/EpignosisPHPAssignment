<?php

require_once 'customRule.php';

abstract class Schema
{
    private array $errors = [];
    private array $customRules = [];

    public function loadData($data)
    {
        foreach ($data as $key => $value)
        {
            $this->{$key} = $value;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    protected function addCustomRule($callback, $message)
    {
        array_push($this->customRules, new CustomRule($message, $callback));
    }

    abstract public function rules(): array;

    public function isValid()
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
        }


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