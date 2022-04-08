<?php

function createSubmissionToken($submissionID, $userID)
{
    global $database;

    $submissionID = filter_var($submissionID, FILTER_SANITIZE_NUMBER_INT);

    $acceptToken = "a" . $userID . md5(uniqid(rand(), true));
    $rejectToken = "r". $userID. md5(uniqid(rand(), true));

    try 
    {
        $stmt = $database->prepare("INSERT INTO submissions_tokens (submission_id, accept_token, reject_token) VALUES (:submission_id, :accept_token, :reject_token)");
        $stmt->execute([
            ':submission_id' => $submissionID,
            ':accept_token' => $acceptToken,
            ':reject_token' => $rejectToken
        ]);
    }
    catch (PDOException $exception) 
    {
        echo $exception->getMessage();
        return false;
    }

    return true;
}

function processSubmissionToken($submissionToken)
{
    global $database;

    $submissionToken = filter_var($submissionToken, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $submissionStatus = "";

    if (substr($submissionToken, 0, 1) == 'a')
    {
        $submissionStatus = 'approved';
    }
    else if (substr($submissionToken, 0, 1) == 'r')
    {
        $submissionStatus = 'rejected';
    }
    else
    {
        return false;
    }

    $submissionID = 0;
    try
    {
        $stmt = $database->prepare("SELECT submission_id FROM submissions_tokens WHERE accept_token = :token OR reject_token = :token");
        $stmt->execute([':token' => $submissionToken]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 0)
        {
            return false;
        }

        $submissionID = $row['submission_id'];
    }
    catch (PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

    try
    {
        $stmt = $database->prepare("UPDATE users_submissions SET status_type = :status_type WHERE id = :submission_id");
        $stmt->execute([':status_type' => $submissionStatus, ':submission_id' => $submissionID]);
    }
    catch (PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

    return true;

}


?>