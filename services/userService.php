<?php

require_once "./connection.php";
require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";
require_once "./models/userDetailsModel.php";
require_once "./schemas/createUserSchema.php";
require_once "./schemas/updateUserSchema.php";

function loginUser(LoginSchema $loginSchema): ?UserDetailsModel
{
    $email = filter_var($loginSchema->email, FILTER_SANITIZE_EMAIL);
    $password = filter_var($loginSchema->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try
    {
        $userModel = UserModel::findOne("email = '$email'");

        if(!$userModel)
        {
            return null;
        }

        if(!password_verify($password, $userModel->password))
        {
            return null;
        }

        $userDetailsModel = UserDetailsModel::findOne("user_id = $userModel->id");

        return $userDetailsModel;
    }
    catch(PDOException $e)
    {
        echo $e->getMessage();
    }

    return null;
}


function getUser($id)
{
    $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);

    $userModel = UserModel::findOne("id = $id");
    
    if(!$userModel)
    {
        return null;
    }
    
    $userDetailsModel = UserDetailsModel::findOne("user_id = $userModel->id");
    
    try
    {
        return composeUserModels($userModel, $userDetailsModel);
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }

    return null;
}

function getAllUsers()
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
                    array_push($mergedUserModels, composeUserModels($user, $userDetail));
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

function createUser(CreateUserSchema $createUserSchema)
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

function updateUser(UpdateUserSchema $updateUserSchema)
{
    $id = filter_var($updateUserSchema->id, FILTER_SANITIZE_NUMBER_INT);
    $firstName = filter_var($updateUserSchema->firstName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName = filter_var($updateUserSchema->lastName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($updateUserSchema->email, FILTER_SANITIZE_EMAIL);
    $userType = filter_var($updateUserSchema->userType, FILTER_SANITIZE_NUMBER_INT);

    try
    {
        if(!UserModel::findOne("id = $id"))
            return false;
        
        UserModel::findOneAndUpdate("id = $id", ['email' => $email]);
        UserDetailsModel::findOneAndUpdate("user_id = $id", ['first_name' => $firstName, 'last_name' => $lastName, 'is_admin' => $userType]);
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

    return true;
}

function doesEmailExist(UpdateUserSchema $updateUserSchema)
{

    $email = filter_var($updateUserSchema->email, FILTER_SANITIZE_EMAIL);
    $userID = filter_var($updateUserSchema->id, FILTER_SANITIZE_NUMBER_INT);
    
    try
    {
        $userModel = UserModel::findOne("email = '$email' AND id != $userID");

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

function composeUserModels(UserModel $userModel, UserDetailsModel $userDetailsModel) : object
{
    //use UserModel's id 
    $id = $userModel->id;

    //combine the two objects to one
    $mergedUserModel = (object) array_merge((array) $userModel, (array) $userDetailsModel);
    
    //add the id to the merged object
    $mergedUserModel->id = $id;

    return $mergedUserModel;
}

?>