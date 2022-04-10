<?php

include_once "./sessionEditor.php";
include_once "protect.php";


function protectAdmin()
{
    protect();
    if(!SessionEditor::getObject(SessionEditor::USER)->is_admin)
    {
        header("Location: /login");
    }
}