<?php

namespace middleware;

use core\SessionEditor;
use core\Middleware;

include_once "./core/sessionEditor.php";
include_once "./core/middleware.php";
include_once "protect.php";


/**
 * Middleware used to restrict routes only to administrator users
 * Redirects user to the login page if they are not an administrator and not an authenticated user
 */

class ProtectAdmin extends Middleware
{
    public static function execute(): bool
    {
        /*In case the user is not even authenticated,
        * SessionEditor::USER will be uninitialized, so we check for that
        */
        if(!SessionEditor::getObject(SessionEditor::USER) ||!SessionEditor::getObject(SessionEditor::USER)->is_admin)
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ["You must be logged in as an administrator to access this page."]);
            header("Location: /login");
            return false;
        }

        return true;
    }
}