<?php

namespace services;

use PDOException;
use models\SubmissionModel;
use models\UserDetailsModel;
use models\UserModel;
use models\SubmissionTokenModel;

require_once './services/userService.php';
require_once './models/userDetailsModel.php';
require_once './models/submissionModel.php';
require_once './models/submissionTokenModel.php';

/**
 * Service for sending emails
 */
class MailService
{
    /**
     * Core function for sending emails,
     * contains all the necessary headers.
     *
     * @param string $email Email address
     * @param string $subject The subject of the email
     * @param string $message The message of the email
     */
    private static function send(string $to, string $subject, string $message): void
    {
        $headers = 'MIME - Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: phpep237@hotmail.com";

        mail($to, $subject, $message, $headers);
    }

    /**
     * Send an email to all the admins.
     *
     * @param string $subject
     * @param string $message
     */
    private static function sendToAllAdmins(string $subject, string $message): void
    {
        $users = UserService::getAll();
        foreach($users as $user)
        {
            if($user->is_admin)
            {
                self::send($user->email, $subject, $message);
            }
        }
    }

    /**
     * Send vacation request email to all the admins.
     * @param SubmissionModel $submission
     */
    public static function sendVacationRequest(SubmissionModel $submission)
    {
        try
        {
            $userDetails = UserDetailsModel::findOne
            ([
                'conditions' => 'user_id = ?',
                'bind' => [$submission->user_id]
            ]);

            $submissionTokens = SubmissionTokenModel::findOne
            ([
                'conditions' => 'submission_id = ?',
                'bind' => [$submission->id]
            ]);

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

            self::sendToAllAdmins($subject, $message);

        }
        catch (PDOException $exception)
        {
            echo $exception->getMessage();
        } 
        
    }

    /**
     * Send submission update email to the submission's user.
     * @param SubmissionModel $submission
     */
    public static function sendSubmissionStatusUpdate(SubmissionModel $submission)
    {
        try
        {
            $user = UserModel::findOne
            ([
                'conditions' => 'id = ?',
                'bind' => [$submission->user_id]
            ]);
            
            $email = $user->email;

            $subject = "Submission Status Update";

            $submissionStatus = $submission->status_type === "approved" ? "accepted" : "rejected";
            $submissionDate = $submission->date_submitted;

            $message = "Dear employee, your supervisor has $submissionStatus your application <br>
            submitted on $submissionDate.";

            self::send($email, $subject, $message);
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
        }
    }
}

?>