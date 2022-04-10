<?php

require_once './core/controller.php';
require_once './services/submissionService.php';
require_once './models/submissionModel.php';
require_once './models/userDetailsModel.php';
require_once './schemas/createSubmissionSchema.php';

/**
 * Controller for the actions of an authenticated user actor
 */
class UserController extends Controller
{
    /**
     * Displays the home page
     * If the user is an administrator,
     * the user is redirected to the admin page.
     */
    public function viewHome()
    {
        if(SessionEditor::getObject(SessionEditor::USER)->is_admin)
        {
            $this->redirect('/admin');
            return;
        }

        $submissions = SubmissionService::getAll(SessionEditor::getObject(SessionEditor::USER)->user_id);
        
        $this->renderView('home', ['submissions' => $submissions]);
    }
    
    /**
     * Displays the create submission form
     */
    public function viewSubmitRequest()
    {
        $this->renderView('submitRequest');
    }
    
    /**
     * Creates a submission
     * If the submission is successfully created,
     * the user is redirected to the home page.
     * Else it displays the create submission form, with
     * the errors.
     */
    public function submitRequestHandler()
    {
        $createSubmissionSchema = new CreateSubmissionSchema();
        $createSubmissionSchema->loadData($_POST);
        
        if(!$createSubmissionSchema->isValid())
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, $createSubmissionSchema->getErrors());
            $this->redirect('/submitRequest');
            return;
        }

        if(!SubmissionService::create($createSubmissionSchema, SessionEditor::getObject(SessionEditor::USER)->user_id))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ["Something went wrong"]);
            $this->redirect('/submitRequest');
            return;
        }
        
        SessionEditor::setAttribute(SessionEditor::ALERTS, ['Submission created']);
        $this->redirect('/home');
    }
}

?>