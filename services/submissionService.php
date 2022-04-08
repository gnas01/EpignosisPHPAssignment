<?php

require_once "./connection.php";
require_once "./models/submission.php";

function getAllSubmissions($userID)
{
    global $database;

    $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);

    $submissions = [];

    try
    {
        $stmt = $database->prepare("SELECT * FROM users_applications WHERE user_id = :user_id ORDER BY date_submitted DESC");
        $stmt->execute([':user_id' => $userID]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row)
        {
            $submission = new Submission();
            $submission->id = $row['id'];
            $submission->dateSubmitted = $row['date_submitted'];
            $submission->vacationStart = $row['vacation_start'];
            $submission->vacationEnd = $row['vacation_end'];
            $submission->reason = $row['reason'];
            $submission->status = $row['status_type'];

            $submissions[] = $submission;
        }
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }

    return $submissions;
}

?>