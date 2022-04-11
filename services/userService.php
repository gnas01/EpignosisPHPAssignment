<?php

namespace services;

use PDOException;
use schemas\LoginSchema;
use schemas\CreateUserSchema;
use schemas\UpdateUserSchema;
use models\UserDetailsModel;
use models\UserModel;

require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";
require_once "./models/userDetailsModel.php";
require_once "./schemas/createUserSchema.php";
require_once "./schemas/updateUserSchema.php";

/**
 * Service for user operations
 */
class UserService
{
    /**
     * Attempts to login the user by comparing
     * his credentials with the ones in the database.
     *
     * @param LoginSchema $loginSchema The data from the login form
     * @return UserDetailsModel|null If the user is verified, returns the user details model, null otherwise
     */
    public static function login(LoginSchema $loginSchema): ?UserDetailsModel
    {
        $email = filter_var($loginSchema->email, FILTER_SANITIZE_EMAIL);
        $password = filter_var($loginSchema->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        try
        {
            $userModel = UserModel::findOne
            ([
                'conditions' => 'email = ?',
                'bind' => [$email]
            ]);

            if(!$userModel)
            {
                return null;
            }

            if(!password_verify($password, $userModel->password))
            {
                return null;
            }

            $userDetailsModel = UserDetailsModel::findOne
            ([
                'conditions' => 'user_id = ?',
                'bind' => [$userModel->id]
            ]);

            return $userDetailsModel;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }

        return null;
    }


    /**
     * Creates a new user object from the id
     * 
     * @param int $userID The user id
     * 
     * @return mixed Returns a combined object of the UserModel
     * and the UserDetailsModel
     */

    public static function get($id)
    {
        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

        $userModel = UserModel::findOne
        ([
            'conditions' => 'id = ?',
            'bind' => [$id]
        ]);
        
        if(!$userModel)
        {
            return null;
        }
        
        $userDetailsModel = UserDetailsModel::findOne
        ([
            'conditions' => 'user_id = ?',
            'bind' => [$userModel->id]
        ]);

        
        try
        {
            return self::composeUserModels($userModel, $userDetailsModel);
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
        }

        return null;
    }

    /**
     * Retrieves all the users from the database
     * @return mixed Returns a combined object array of the UserModel
     * and the UserDetailsModel
     */
    public static function getAll()
    {
        try
        {
            $users = UserModel::findAll();
            $userDetails = UserDetailsModel::findAll();

            $mergedUserModels = [];
            
            /* Map user credentials with user details*/
            foreach($users as $user)
            {
                foreach($userDetails as $userDetail)
                {
                    if($user->id == $userDetail->user_id)
                    {
                        array_push($mergedUserModels, self::composeUserModels($user, $userDetail));
                    }
                }
            }
            return $mergedUserModels;
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
        }
    }

    /**
     * Creates a new user from create user form data.
     * 
     * @param CreateUserSchema $createUserSchema The data from the create user form
     * 
     * @return bool Returns true if the user was created, false otherwise
     */
    public static function create(CreateUserSchema $createUserSchema): bool
    {
        $firstName = filter_var($createUserSchema->firstName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_var($createUserSchema->lastName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($createUserSchema->email, FILTER_SANITIZE_EMAIL);
        $password = filter_var($createUserSchema->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $userType = filter_var($createUserSchema->userType, FILTER_SANITIZE_NUMBER_INT);

        try
        {
            $userModel = new UserModel();
            $userModel->email = $email;
            $userModel->password = password_hash($password, PASSWORD_DEFAULT);
            $userModel->save();
            
            $userDetailsModel = new UserDetailsModel();
            $userDetailsModel->first_name = $firstName;
            $userDetailsModel->last_name = $lastName;
            $userDetailsModel->user_id = $userModel->id;
            $userDetailsModel->is_admin = $userType;
            $userDetailsModel->save();
            
            return true;
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
        }

        return false;
    }

    /**
     * Updates a user from the update user form data.
     *
     * @param UpdateUserSchema $updateUserSchema The data from the update user form
     * @return bool Returns true if the update was successful, false otherwise
     */
    public static function update(UpdateUserSchema $updateUserSchema): bool
    {
        $id = filter_var($updateUserSchema->id, FILTER_SANITIZE_NUMBER_INT);
        $firstName = filter_var($updateUserSchema->firstName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_var($updateUserSchema->lastName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($updateUserSchema->email, FILTER_SANITIZE_EMAIL);
        $userType = filter_var($updateUserSchema->userType, FILTER_SANITIZE_NUMBER_INT);

        try
        {
            $userModel = UserModel::findOne
            ([
                'conditions' => 'id = ?',
                'bind' => [$id]
            ]);

            if(!$userModel)
            {
                return false;
            }
            UserModel::findOneAndUpdate((['conditions' => 'id = ?', 'bind' => [$id]]), ['email' => $email]);
            UserDetailsModel::findOneAndUpdate((['conditions' => 'user_id = ?', 'bind' => [$id]]), ['first_name' => $firstName, 'last_name' => $lastName, 'is_admin' => $userType]);
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Helper function to detect if the updated email is already in use
     * 
     * @param UpdateUserSchema $updateUserSchema The data from the update user form.
     * 
     * @return bool Returns true if the email is already in use, false otherwise
     */
    public static function doesEmailExist(UpdateUserSchema $updateUserSchema): bool
    {

        $email = filter_var($updateUserSchema->email, FILTER_SANITIZE_EMAIL);
        $userID = filter_var($updateUserSchema->id, FILTER_SANITIZE_NUMBER_INT);
        
        try
        {
            $userModel = UserModel::findOne
            ([
                'conditions' => 'email = ? AND id != ?',
                'bind' => [$email, $userID]
            ]);

            if(!$userModel)
            {
                return false;
            }
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
            return false;
        }
        
        return true;

    }

    /**
     * Helper function to combine the user model and user details model
     * 
     * @param UserModel $userModel The user model
     * @param UserDetailsModel $userDetailsModel The user details model
     * 
     * 
     * @return stdclass Returns the combined user model
     */
    private static function composeUserModels(UserModel $userModel, UserDetailsModel $userDetailsModel) : object
    {
        //use UserModel's id 
        $id = $userModel->id;

        //combine the two objects to one
        $mergedUserModel = (object) array_merge((array) $userModel, (array) $userDetailsModel);
        
        //add the id to the merged object
        $mergedUserModel->id = $id;

        return $mergedUserModel;
    }
}


?>