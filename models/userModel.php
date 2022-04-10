<?php

namespace models;

use core\SQLModel;

include_once "./core/sqlModel.php";

/**
 * Model for the users table
 * Stores the credentials of a user.
 */
class UserModel extends SQLModel
{
    public string $email = "";
    public string $password = "";

    public static function getTableName(): string
    {
        return 'users';
    }
}

?>