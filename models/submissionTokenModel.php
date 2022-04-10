<?php

namespace models;

use core\SQLModel;

include_once "./core/sqlModel.php";

/**
 * Model for the submissions_tokens table.
 * Stores the tokens to either approve or reject a submission.
 */
class SubmissionTokenModel extends SQLModel
{
    public int $submission_id;
    public string $accept_token;
    public string $reject_token;

    public static function getTableName(): string
    {
        return 'submissions_tokens';
    }
}

?>