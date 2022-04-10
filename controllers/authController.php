<?php

require_once "./core/controller.php";

require_once "./services/userService.php";
require_once "./schemas/loginSchema.php";
require_once "./models/userModel.php";

class AuthController extends Controller
{
    public function viewLogin()
    {
        if(SessionEditor::getAttribute(SessionEditor::AUTHENTICATED))
        {
            $this->redirect('/home');
            return;
        }

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

        $userDetailsModel = loginUser($loginSchema);

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
    
    public function logoutUserHandler()
    {
        SessionEditor::clear();
        $this->redirect('/login');
    }
}

?>