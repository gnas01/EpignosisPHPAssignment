<?php

include_once "./core/sqlModel.php";

class UserModel extends SQLModel
{
    public string $email = "";
    public string $password = "";

    public static function getTableName()
    {
        return 'users';
    }
}

?>