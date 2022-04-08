<?php

require_once './core/controller.php';
require_once './services/submissionService.php';
require_once './models/submissionModel.php';
require_once './schemas/createSubmissionSchema.php';

class UserController extends Controller
{
    public function viewHome()
    {
        if(SessionEditor::getAttribute(SessionEditor::USER)->isAdmin)
        {
            $this->redirect('/admin');
            return;
        }

        $submissions = getAllSubmissions(SessionEditor::getAttribute(SessionEditor::USER)->id);
        
        $this->renderView('home', ['submissions' => $submissions]);
    }
    
    public function viewSubmitRequest()
    {
        $this->renderView('submitRequest');
    }
    
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
        
        if(!createSubmission($createSubmissionSchema, SessionEditor::getAttribute(SessionEditor::USER)->id))
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