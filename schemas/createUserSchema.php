<?php

require_once 'schema.php';

class CreateUserSchema extends Schema
{
    public string $firstName;
    public string $lastName;
    public string $email;
    public string $password;
    public string $confirmPassword;
    public string $userType;

    public function rules(): array
    {
        $this->addCustomRule($this->validateUserType(), 'The user type must be either "Employee" or "Admin"');

        return [
            'firstName' => [
                'required' => true,
                'min' => 3,
                'max' => 50
            ],
            'lastName' => [
                'required' => true,
                'min' => 3,
                'max' => 50
            ],
            'email' => [
                'required' => true,
                'min' => 3,
                'max' => 254,
                'email' => true
            ],
            'password' => [
                'required' => true,
                'min' => 3,
                'max' => 20
            ],
            'confirmPassword' => [
                'match' => true, 'match' => 'password'
            ],
            'userType' => [
                'required' => true,
            ],
        ];
    }

    private function validateUserType()
    {
        if($this->userType != '0' && $this->userType != '1')
        {
            return false;
        }

        return true;
    }

    
}