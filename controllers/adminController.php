<?php

require_once 'controller.php';

require_once './schemas/createUserSchema.php';
require_once './services/userService.php';
require_once './sessionEditor.php';

class AdminController extends Controller
{
    public function viewAdmin()
    {
        $this->renderView('admin');
    }

    public function viewEditUser()
    {
        $this->renderView('editUser');
    }

    public function viewCreateUser()
    {
        $this->renderView('createUser');
    }

    public function createUserHandler()
    {
        $createUserSchema = new CreateUserSchema();
        $createUserSchema->loadData($_POST);
        
        if(!$createUserSchema->isValid())
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, $createUserSchema->getErrors());
            $this->redirect('/createUser');
            return;
        }

        if(!createUser($createUserSchema))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User already exists']);
            $this->redirect('/createUser');
            return;
        }

        SessionEditor::setAttribute(SessionEditor::ALERTS, ['User created']);
        $this->redirect('/createUser');
    }
}

?>