<?php

require_once "./connection.php";
require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";

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

?>