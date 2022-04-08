<?php

require_once "./connection.php";
require_once "./models/submissionModel.php";
require_once "./schemas/createSubmissionSchema.php";

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
            $submission = new SubmissionModel();
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

function createSubmission(CreateSubmissionSchema $createSubmissionSchema, $userID)
{
    global $database;

    $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);

    try
    {
        $stmt = $database->prepare("INSERT INTO users_applications (user_id, vacation_start, vacation_end, reason) VALUES (:user_id, :vacation_start, :vacation_end, :reason)");
        $stmt->execute([
            ':user_id' => $userID,
            ':vacation_start' => $createSubmissionSchema->startDate,
            ':vacation_end' => $createSubmissionSchema->endDate,
            ':reason' => $createSubmissionSchema->reason

        ]);

    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

    return true;
}

?>