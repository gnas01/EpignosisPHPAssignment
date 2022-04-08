<?php

require_once "./connection.php";
require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";
require_once "./schemas/createUserSchema.php";

function loginUser(LoginSchema $loginSchema): int
{
    global $database;

    $email = filter_var($loginSchema->email, FILTER_SANITIZE_EMAIL);
    $password = filter_var($loginSchema->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try
    {
        $stmt = $database->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount() > 0)
        {
            $userId = $row['id'];
            $stmt = $database->prepare("SELECT password FROM users_credentials WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $userId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if(password_verify($password, $row['password']))
            {
                return $userId;
            }
        }
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }

    return -1;
}

function getUser($id)
{
    global $database;

    $userModel = new UserModel();

    try
    {
        $stmt = $database->prepare("SELECT * FROM users_details WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount() > 0)
        {
            $userModel->firstName = $row['first_name'];
            $userModel->lastName = $row['last_name'];
            $userModel->isAdmin = $row['is_admin'];
        }


        $stmt = $database->prepare("SELECT email FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount() > 0)
        {
            $userModel->id = $id;
            $userModel->email = $row['email'];

            return $userModel;
        }
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }

    return null;
}

function getAllUsers()
{
    global $database;

    $users = [];

    try
    {
        $stmt = $database->prepare("SELECT * FROM users_details");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($stmt->rowCount() > 0)
        {
            foreach($rows as $row)
            {
                array_push($users, getUser($row['id']));
            }

            return $users;
        }
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }
}

function createUser(CreateUserSchema $createUserSchema)
{
    global $database;

    $firstName = filter_var($createUserSchema->firstName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastName = filter_var($createUserSchema->lastName, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($createUserSchema->email, FILTER_SANITIZE_EMAIL);
    $password = filter_var($createUserSchema->password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userType = filter_var($createUserSchema->userType, FILTER_SANITIZE_NUMBER_INT);

    try
    {
        $stmt = $database->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount() > 0)
        {
            return false;
        }

        $stmt = $database->prepare("INSERT INTO users (email) VALUES (:email)");
        $stmt->execute([':email' => $email]);

        $stmt = $database->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $userId = $row['id'];

        $stmt = $database->prepare("INSERT INTO users_details (user_id, first_name, last_name, is_admin) VALUES (:user_id, :firstName, :lastName, :isAdmin)");
        $stmt->execute([':user_id' => $userId, ':firstName' => $firstName, ':lastName' => $lastName, ':isAdmin' => $userType]);

        $stmt = $database->prepare("INSERT INTO users_credentials (user_id, password) VALUES (:user_id, :password)");
        $stmt->execute([':user_id' => $userId, ':password' => password_hash($password, PASSWORD_DEFAULT)]);
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

    return true;
}

?>