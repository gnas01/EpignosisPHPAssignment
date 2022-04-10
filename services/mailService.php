<?php

require_once './services/userService.php';
require_once './models/userDetailsModel.php';
require_once './models/submissionModel.php';
require_once './models/submissionTokenModel.php';

function sendMail($to, $subject, $message)
{
    $headers = 'MIME - Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: phpep237@hotmail.com";

    mail($to, $subject, $message, $headers);
}

function sendMailToAllAdmins($subject, $message)
{
    $users = getAllUsers();
    foreach($users as $user)
    {
        if($user->is_admin)
        {
            sendMail($user->email, $subject, $message);
        }
    }
}

function vacationRequestMail(SubmissionModel $submission)
{
    try
    {
        $userDetails = UserDetailsModel::findOne("user_id = $submission->user_id");
        $submissionTokens = SubmissionTokenModel::findOne("submission_id = $submission->id");

        $subject = "Vacation Request";

        $fullname = $userDetails->first_name . " " . $userDetails->last_name;

        $vacationStart = $submission->vacation_start;
        $vacationEnd = $submission->vacation_end;
        $reason = $submission->reason;

        $baseLink = "http://localhost:8080/updateSubmission?token=";

        $acceptLink = $baseLink . $submissionTokens->accept_token;
        $rejectLink = $baseLink . $submissionTokens->reject_token;

        $message = "Dear supervisor, employee $fullname requested for some time off, <br>starting on 
        $vacationStart and ending on $vacationEnd,<br> stating the reason:
        $reason <br>
        Click on one of the below links to approve or reject the application: <br>
        <a href='$acceptLink'>Accept</a> - <a href='$rejectLink'>Reject</a>";

        sendMailToAllAdmins($subject, $message);

    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    } 
    
}

function submissionStatusUpdateMail(SubmissionModel $submission)
{
    try
    {
        $user = UserModel::findOne("id = $submission->user_id");
        
        $email = $user->email;

        $subject = "Submission Status Update";

        $submissionStatus = $submission->status_type === "approved" ? "accepted" : "rejected";
        $submissionDate = $submission->date_submitted;

        $message = "Dear employee, your supervisor has $submissionStatus your application <br>
        submitted on $submissionDate.";

        sendMail($email, $subject, $message);
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }
}
?>