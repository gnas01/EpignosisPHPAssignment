<?php

require_once "controller.php";

require_once "./services/userService.php";
require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";

class AuthController extends Controller
{
    public function viewLogin()
    {
        $this->renderView('signin');
    }

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

        $user_id = loginUser($loginSchema);

        if($user_id === 0)
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ['Invalid email or password']);
            $this->redirect('/login');
            return;
        }

        $userModel = getUser($user_id);

        SessionEditor::setAttribute(SessionEditor::USER, $userModel);
        SessionEditor::setAttribute(SessionEditor::AUTHENTICATED, true);

        if($userModel->isAdmin)
        {
            $this->redirect('/admin');
        }
        else
        {
            $this->redirect('/home');
        }
    }
    
    public function logoutUserHandler()
    {
        SessionEditor::clear();
        $this->redirect('/login');
    }
}

?>