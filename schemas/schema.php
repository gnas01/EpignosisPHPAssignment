<?php

abstract class Schema
{
    private array $errors = [];

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
        }
        return empty($this->errors);
    }
}

?>