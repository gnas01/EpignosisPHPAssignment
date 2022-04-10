<?php

namespace schemas;

use core\Schema;

require_once './core/schema.php';

/**
 * Schema used to validate the data
 * from the user update form.
 */
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
                'max' => 50
            ],
            'lastName' => 
            [
                'required' => true,
                'min' => 3,
                'max' => 50
            ],
            'email' => 
            [
                'required' => true,
                'min' => 3,
                'max' => 254,
                'email' => true
            ],
            'userType' => 
            [
                'required' => true,
            ],
        ];
    }

    /**
     * Custom rule to validate if the user type,
     * selected in the drop down list is either "Employee" or "Admin".
     */
    private function validateUserType(): bool
    {
        if($this->userType != '0' && $this->userType != '1')
        {
            return false;
        }

        return true;
    }

    
}
?>