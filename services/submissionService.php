<?php

require_once "./connection.php";
require_once "./models/submissionModel.php";
require_once "./models/submissionTokenModel.php";
require_once "./models/userModel.php";
require_once "./schemas/createSubmissionSchema.php";
require_once "./services/mailService.php";

function getAllSubmissions($userID)
{
    $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);

    $submissionModels = [];

    try
    {
        //in dec order
        $submissionModels = SubmissionModel::find("user_id = $userID ORDER BY date_submitted DESC");
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
    }

    return $submissionModels;
}

function createSubmission(CreateSubmissionSchema $createSubmissionSchema, $userID)
{
    $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
    //sanitize 
    $vacationStart = filter_var($createSubmissionSchema->startDate, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $vacationEnd = filter_var($createSubmissionSchema->endDate, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $reason = filter_var($createSubmissionSchema->reason, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    //find if user exists
    if(!UserModel::find($userID))
    {
        return false;
    }

    try
    {
        /* By not initalizing certain fields
           we leave it up to the sql database
           to set the default values for these fields */
        $submissionModel = new SubmissionModel();
        $submissionModel->user_id = $userID;
        $submissionModel->vacation_start = $vacationStart;
        $submissionModel->vacation_end = $vacationEnd;
        $submissionModel->reason = $reason;
        $submissionModel->save();
        
        //create a token for the submission
         if(!createSubmissionToken($submissionModel))
         {
             return false;
         }
         
         vacationRequestMail($submissionModel);

    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

    return true;
}

function createSubmissionToken(SubmissionModel $submissionModel)
{
    $submissionID = filter_var($submissionModel->id, FILTER_SANITIZE_NUMBER_INT);
    
    $acceptToken = "a" . $submissionModel->user_id . md5(uniqid(rand(), true));
    $rejectToken = "r". $submissionModel->user_id. md5(uniqid(rand(), true));
    
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

function processSubmissionToken($submissionToken)
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
        $submissionTokenModel = SubmissionTokenModel::findOne("accept_token = '$submissionToken' OR reject_token = '$submissionToken'");

        if(!$submissionTokenModel)
        {
            return false;
        }
        
        $submissionModel = SubmissionModel::findOne("id = $submissionTokenModel->submission_id");
        
        if(!$submissionModel)
        {
            return false;
        }
        
        if(!SubmissionModel::findOneAndUpdate("id = $submissionModel->id", ['status_type' => $submissionStatus]))
        {
            return false;
        }
        
        submissionStatusUpdateMail($submissionModel);
        
        return true;
    }
    catch(PDOException $exception)
    {
        echo $exception->getMessage();
        return false;
    }

}


?>