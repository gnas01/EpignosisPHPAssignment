<?php

namespace middleware;

use core\SessionEditor;

/**
 * Middleware used to restrict routes only to administrator users
 */

include_once "./core/sessionEditor.php";
include_once "protect.php";


/**
 * Middleware used to restrict routes only to administrator users
 * Redirects user to the login page if they are not an administrator and not an authenticated user
 */

function protectAdmin()
{
    protect();
    if(!SessionEditor::getObject(SessionEditor::USER)->is_admin)
    {
        header("Location: /login");
    }
}