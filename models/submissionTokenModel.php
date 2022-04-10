<?php

include_once "./core/sqlModel.php";

class SubmissionTokenModel extends SQLModel
{
    public int $submission_id;
    public string $accept_token;
    public string $reject_token;

    public static function getTableName()
    {
        return 'submissions_tokens';
    }
}

?>