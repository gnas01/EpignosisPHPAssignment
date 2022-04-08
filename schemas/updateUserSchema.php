<?php

require_once 'schema.php';

class UpdateUserSchema extends Schema
{
    public string $id;
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
            'id' => 
            [
                'required' => true,
                'integer' => true,
            ],
            'firstName' => 
            [
                'required' => true,
                'min' => 3,
                'max' => 20
            ],
            'lastName' => 
            [
                'required' => true,
                'min' => 3,
                'max' => 20
            ],
            'email' => 
            [
                'required' => true,
                'min' => 3,
                'max' => 20,
                'email' => true
            ],
            'userType' => 
            [
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
?>