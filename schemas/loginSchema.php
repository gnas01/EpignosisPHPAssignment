<?php

require_once './core/schema.php';

/**
 * Schema used to validate the data
 * from the Login form.
 */
class LoginSchema extends Schema
{
    public string $email;
    public string $password;

    public function rules(): array
    {
        return [
            'email' => [
                'required' => true,
                'min' => 3,
                'max' => 50,
                'email' => true
            ],
            'password' => [
                'required' => true,
                'min' => 3,
                'max' => 50
            ]
        ];
    }
}

?>
