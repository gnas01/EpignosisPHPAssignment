<?php

require_once 'schema.php';

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
                'max' => 20,
                'email' => true
            ],
            'password' => [
                'required' => true,
                'min' => 3,
                'max' => 20
            ]
        ];
    }
}

?>
