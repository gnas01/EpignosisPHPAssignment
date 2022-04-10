<?php

include_once "./core/sqlModel.php";

class UserDetailsModel extends SQLModel
{
    public int $user_id = 0;
    public string $first_name = "";
    public string $last_name = "";
    public bool $is_admin = false;

    public static function getTableName()
    {
        return 'users_details';
    }
}

?>