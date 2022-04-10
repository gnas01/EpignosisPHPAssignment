<?php

include_once "./core/sqlModel.php";

/**
 * Model for the users_submissions table.
 * Stores the submission that a user has made.
 */
class SubmissionModel extends SQLModel
{
    public string $user_id;
    public string $date_submitted;
    public string $vacation_start;
    public string $vacation_end;
    public string $reason;
    public string $status_type;
    
    public static function getTableName(): string
    {
        return 'users_submissions';
    }

    /**
     * Helper function to calculate the amount of days 
     * between the start and the end date.
     * 
     * @return string The amount of days between the start and the end date.
     */
    public function getDaysRequested() : string
    {
        $start = new DateTime($this->vacation_start);
        $end = new DateTime($this->vacation_end);

        $interval = $start->diff($end);

        return $interval->format('%a');
    }
}

?>