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
            $_SESSION['errors'] = $loginSchema->getErrors();
            $this->redirect('/login');
            return;
        }

        $user_id = loginUser($loginSchema);

        if($user_id === -1)
        {
            $_SESSION['errors'] = ['Invalid email or password'];
            $this->redirect('/login');
            return;
        }

        $userModel = getUser($user_id);

        $_SESSION['user'] = $userModel;
        $_SESSION['logged_in'] = true;

        if($userModel->isAdmin)
        {
            $this->redirect('/admin');
        }
        else
        {
            $this->redirect('/home');
        }
    }
}

?>