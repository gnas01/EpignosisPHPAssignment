<?php

include_once "./sessionEditor.php";



function protectAdmin()
{
    include_once "./protect.php";
    if(!SessionEditor::getAttribute(SessionEditor::USER)->isAdmin)
    {
        header("Location: /login");
    }
}