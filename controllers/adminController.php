<?php

require_once 'controller.php';

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
}

?>