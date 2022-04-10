<?php

require_once "mailService.php";

/**
 * Service for submissions token operations
 */
class SubmissionTokenService
{
    /**
     * Create the accept and reject tokens for a submission.
     * The tokens are generated using an md5 hash of a random string and user id.
     * The accept token start with "a" prefix and the reject token start with "r" prefix.
     *
     * @param SubmissionModel $submissionModel The submission model to create the tokens for
     * @return boolean True if the submission token was created, false otherwise
     */
    public static function create(SubmissionModel $submissionModel): bool
    {
        $submissionID = filter_var($submissionModel->id, FILTER_SANITIZE_NUMBER_INT);

        $acceptToken = "a" . $submissionModel->user_id . md5(uniqid(rand(), true));
        $rejectToken = "r" . $submissionModel->user_id . md5(uniqid(rand(), true));

        try 
        {
            $submissionTokenModel = new SubmissionTokenModel();
            $submissionTokenModel->submission_id = $submissionID;
            $submissionTokenModel->accept_token = $acceptToken;
            $submissionTokenModel->reject_token = $rejectToken;
            $submissionTokenModel->save();

            return true;
        } 
        catch (PDOException $exception) 
        {
            echo $exception->getMessage();
            return false;
        }

        return false;
    }

    
    /**
     * Processes the submission token from the given data.
     * Checks the prefix of the token and marks it as accepted or rejected.
     * Retrieves the necessary data from the database and sends an email to the user.
     * @param $submissionToken The token
     */
    public static function process($submissionToken): bool
    {
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

        try 
        {
            $submissionTokenModel = SubmissionTokenModel::findOne
            ([
                    'conditions' => 'accept_token = ? OR reject_token = ?',
                    'bind' => [$submissionToken, $submissionToken]
            ]);

            if (!$submissionTokenModel) 
            {
                return false;
            }

            $submissionModel = SubmissionModel::findOne
            ([
                    'conditions' => 'id = ?',
                    'bind' => [$submissionTokenModel->submission_id]
            ]);

            if (!$submissionModel) 
            {
                return false;
            }

            $submissionModel = SubmissionModel::findOneAndUpdate
            (
                ([
                    'conditions' => 'id = ?',
                    'bind' => [$submissionTokenModel->submission_id],
                ]),
                [
                    'status_type' => $submissionStatus
                ]
            );

            if (!$submissionModel) 
            {
                return false;
            }

            MailService::sendSubmissionStatusUpdate($submissionModel);

            return true;
        } 
        catch (PDOException $exception) 
        {
            echo $exception->getMessage();
            return false;
        }
    }

}

?>