<?php

include_once "./core/sqlModel.php";
class SubmissionModel extends SQLModel
{
    public string $user_id;
    public string $date_submitted;
    public string $vacation_start;
    public string $vacation_end;
    public string $reason;
    public string $status_type;
    
    public static function getTableName()
    {
        return 'users_submissions';
    }

    public function getDaysRequested()
    {
        $start = new DateTime($this->vacation_start);
        $end = new DateTime($this->vacation_end);

        $interval = $start->diff($end);

        return $interval->format('%a');
    }
}

?>