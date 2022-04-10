<?php

namespace controllers;

use core\Controller;
use core\SessionEditor;
use schemas\LoginSchema;
use services\UserService;

require_once "./core/controller.php";

require_once "./services/userService.php";
require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";

/**
 * Controller for authentication actions.
 */
class AuthController extends Controller
{
    /**
     * Displays the login form
     * If the user is already logged in,
     * the user is redirected to the home page.
     */
    public function viewLogin()
    {
        if(SessionEditor::getAttribute(SessionEditor::AUTHENTICATED))
        {
            $this->redirect('/home');
            return;
        }

        $this->renderView('signin');
    }

    /**
     * Logs in a user
     * If the user is already logged in,
     * the user is redirected to the home page.
     * Else it checks the user type (admin or not)
     * and redirects to the appropriate page.
     */
    public function loginUserHandler()
    {
        $loginSchema = new LoginSchema();
        $loginSchema->loadData($_POST);

        if(!$loginSchema->isValid())
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, $loginSchema->getErrors());
            $this->redirect('/login');
            return;
        }

        $userDetailsModel = UserService::login($loginSchema);

        if(!$userDetailsModel)
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['Invalid email or password']);
            $this->redirect('/login');
            return;
        }

        SessionEditor::setObject(SessionEditor::USER, $userDetailsModel);
        SessionEditor::setAttribute(SessionEditor::AUTHENTICATED, true);
        if($userDetailsModel->is_admin)
        {
            $this->redirect('/admin');
        }
        else
        {
            $this->redirect('/home');
        }
    }
    
    /**
     * Handler for logging out the user.
     * Clears session and redirects to the login page.
     */
    public function logoutUserHandler()
    {
        SessionEditor::clear();
        $this->redirect('/login');
    }
}

?>