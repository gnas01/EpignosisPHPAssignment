<?php

require_once 'controller.php';

require_once './schemas/createUserSchema.php';
require_once './schemas/updateUserSchema.php';

require_once './services/userService.php';

require_once './sessionEditor.php';

class AdminController extends Controller
{
    public function viewAdmin()
    {
        $this->renderView('admin');
    }

    public function viewUpdateUser()
    {
        $selectedUserID = 0;

        if(isset($_GET['id']))
        {
            $selectedUserID = $_GET['id'];
        }

        $selectedUser = getUser($selectedUserID);

        if(!$selectedUser === null)
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ["User not found"]);
        }
        else
        {
            SessionEditor::setAttribute(SessionEditor::SELECTED_USER, $selectedUser);
        }

        $this->renderView('updateUser');
    }

    public function viewCreateUser()
    {
        SessionEditor::removeAttribute(SessionEditor::SELECTED_USER);
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
    
    public function updateUserHandler()
    {
        $updateUserSchema = new UpdateUserSchema();
        $updateUserSchema->loadData($_POST);
        
        if(!$updateUserSchema->isValid())
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, $updateUserSchema->getErrors());
        }
        else if(!getUser($updateUserSchema->id))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User does not exist']);
        }
        else if(doesEmailExist($updateUserSchema))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User already exists']);
        }
        else if(!updateUser($updateUserSchema))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['Something went wrong']);
        }
        else
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User updated']);
        }

        $this->redirect('/updateUser?id=' . $_POST['id']);
    }
}

?>