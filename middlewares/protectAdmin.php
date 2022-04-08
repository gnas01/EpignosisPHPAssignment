<?php

include_once "./sessionEditor.php";
include_once "protect.php";


function protectAdmin()
{
    protect();
    if(!SessionEditor::getAttribute(SessionEditor::USER)->isAdmin)
    {
        header("Location: /login");
    }
}