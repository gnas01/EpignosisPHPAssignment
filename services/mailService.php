<?php

require_once './services/userService.php';
require_once './models/userModel.php';
require_once './models/submissionModel.php';

function sendMail($to, $subject, $message)
{
    $headers = "From: phpep237@hotmail.com";

    mail($to, $subject, $message, $headers);
}

function sendMailToAllAdmins($subject, $message)
{
    $users = getAllUsers();

    foreach($users as $user)
    {
        if($user->isAdmin)
        {
            sendMail($user->email, $subject, $message);
        }
    }
}

function vacationRequestMail(UserModel $user, SubmissionModel $submission, $submissionTokenAccept, $submissionTokenReject)
{
    $subject = "Vacation Request";

    $fullname = $user->firstName . " " . $user->lastName;

    $vacationStart = $submission->vacationStart;
    $vacationEnd = $submission->vacationEnd;
    $reason = $submission->reason;

    $baseLink = "http://localhost:8080/updateSubmission?token=";

    $acceptLink = $baseLink . $submissionTokenAccept;
    $rejectLink = $baseLink . $submissionTokenReject;

    $message = "Dear supervisor, employee $fullname requested for some time off, starting on
    $vacationStart and ending on $vacationEnd, stating the reason:
    $reason
    Click on one of the below links to approve or reject the application:
    <a href='$acceptLink'>Accept</a> - <a href='$rejectLink'>Reject</a>";

    sendMailToAllAdmins($subject, $message);
}

function submissionStatusUpdateMail($userEmail, SubmissionModel $submission)
{
    $subject = "Submission Status Update";

    $submissionStatus = $submission->status === "approved" ? "accepted" : "rejected";
    $submissionDate = $submission->dateSubmitted;

    $message = "Dear employee, your supervisor has $submissionStatus your application
    submitted on $submissionDate.";

    sendMail($userEmail, $subject, $message);
}
?>