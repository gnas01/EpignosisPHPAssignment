<?php

require_once './core/schema.php';

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
