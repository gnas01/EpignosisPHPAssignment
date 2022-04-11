<?php

namespace services;

use PDOException;
use models\SubmissionModel;
use models\UserModel;
use schemas\CreateSubmissionSchema;

require_once "./models/submissionModel.php";
require_once "./models/submissionTokenModel.php";
require_once "./models/userModel.php";
require_once "./schemas/createSubmissionSchema.php";
require_once "./services/mailService.php";
require_once "./services/submissionTokenService.php";

/**
 * Service for submissions operations
 */
class SubmissionService
{
    /**
     * Returns all submissions of a user.
     * @param int $userId The user id
     * @return array The submissions
     */
    public static function getAll($userID): array
    {
        $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);

        $submissionModels = [];

        try
        {
            $submissionModels = SubmissionModel::find
            ([
                'conditions' => 'user_id = ? ORDER BY date_submitted DESC',
                'bind' => [$userID]
            ]);
        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
        }

        return $submissionModels;
    }

    /**
     * Creates a new submission from the given data
     * and sends an eimail to all the admins of the database.
     * @param CreateSubmissionSchema $createSubmissionSchema The data from the form
     * @param int $userID The user id
     * @return bool True if the submission was created, false otherwise
     */
    public static function create(CreateSubmissionSchema $createSubmissionSchema, $userID): bool
    {
        $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);
        //sanitize 
        $vacationStart = filter_var($createSubmissionSchema->startDate, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vacationEnd = filter_var($createSubmissionSchema->endDate, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $reason = filter_var($createSubmissionSchema->reason, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        //find if user exists
        $userModel = UserModel::findOne
        ([
            'conditions' => 'id = ?',
            'bind' => [$userID]
        ]);

        if(!$userModel)
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
             if(!SubmissionTokenService::create($submissionModel))
             {
                 return false;
             }
             
             MailService::sendVacationRequest($submissionModel);

        }
        catch(PDOException $exception)
        {
            echo $exception->getMessage();
            return false;
        }

        return true;
    }
}

?>