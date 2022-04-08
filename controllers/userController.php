<?php

require_once 'controller.php';
require_once './services/submissionService.php';
require_once './models/submission.php';

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
}

?>