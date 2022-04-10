<?php

namespace controllers;

use core\Controller;
use core\SessionEditor;
use middleware\ProtectAdmin;
use middleware\Protect;
use services\UserService;
use services\SubmissionTokenService;
use schemas\CreateUserSchema;
use schemas\UpdateUserSchema;

require_once './core/controller.php';

require_once './schemas/createUserSchema.php';
require_once './schemas/updateUserSchema.php';

require_once './services/userService.php';
require_once './services/submissionService.php';
require_once './services/submissionTokenService.php';

require_once './middlewares/protectAdmin.php';
require_once './middlewares/protect.php';
require_once './core/sessionEditor.php';

/**
 * Controller for the actions of an administrator actor.
 */
class AdminController extends Controller
{
    /**
     * Displays the admin page with the list of users.
     */
    public function viewAdmin()
    {
        $users = UserService::getAll();
        $this->renderView('admin', ['users' => $users]);
    }

    /**
     * Displays the update user form with the user details.
     */
    public function viewUpdateUser()
    {
        $selectedUserID = 0;

        if(isset($_GET['id']))
        {
            $selectedUserID = $_GET['id'];
        }

        $selectedUser = UserService::get($selectedUserID);

        if(!$selectedUser)
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ["User not found"]);
        }

        /*if selected user could not be found
        updateUser view will treat the null value accordingly*/
        $this->renderView('updateUser', ['selectedUser' => $selectedUser]);
    }

    /**
     * Displays the create user form
     * The create user form uses a common layout with the update user form,
     * as a result the submit button is named differently.
     */
    public function viewCreateUser()
    {
        SessionEditor::removeAttribute(SessionEditor::SELECTED_USER);
        $this->renderView('createUser', ['submitValue' => 'Create user']);
    }

    /**
     * Handler for creating a new user.
     */
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

        if(!UserService::create($createUserSchema))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User already exists']);
            $this->redirect('/createUser');
            return;
        }

        SessionEditor::setAttribute(SessionEditor::ALERTS, ['User created']);
        $this->redirect('/createUser');
    }
    
    /**
     * Handler for updating a user.
     */
    public function updateUserHandler()
    {
        $updateUserSchema = new UpdateUserSchema();
        $updateUserSchema->loadData($_POST);
        
        if(!$updateUserSchema->isValid())
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, $updateUserSchema->getErrors());
        }
        else if(!UserService::get($updateUserSchema->id))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User does not exist']);
        }
        else if(UserService::doesEmailExist($updateUserSchema))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User already exists']);
        }
        else if(!UserService::update($updateUserSchema))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['Something went wrong']);
        }
        else
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['User updated']);
        }

        $this->redirect('/updateUser?id=' . $_POST['id']);
    }
    
    /**
     * Handler for updating a submission's status.
     * If the user is not signed in it renders the login page.
     * The current url is saved in the session for the redirect after login.
     * When the user is redirected, the ProtectAdmin middlware is used here
     * to ensure that only an administrator can access this page.
     * then it retrieves the token from the URL
     * and sends it for processing.
     */
    public function updateSubmissionHandler()
    {

        if(!SessionEditor::getAttribute(SessionEditor::AUTHENTICATED))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['You must sign in to continue']);
            SessionEditor::setAttribute(SessionEditor::REDIRECTION, $_SERVER['REQUEST_URI']);

            $this->renderView('signIn');
            return;
        }
        
        ProtectAdmin::execute();
        
        
        if(!isset($_GET['token']))
        {   
            echo "Token not provided";
            return;
        }
        
        $submissionToken = $_GET['token'];
        
        if(!SubmissionTokenService::process($submissionToken))
        {
            echo "Something went wrong";
            return;
        }
        
        echo "Submission status updated";
        
    }
}

?>