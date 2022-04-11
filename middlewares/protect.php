<?php

/**contains all middleware components*/
namespace middleware;

use core\SessionEditor;
use core\Middleware;


include_once './core/middleware.php';
include_once "./core/sessionEditor.php";

/**
 * Middleware used to restrict routes only to authenticated users
 * Non authenticated users will be redirected to the login page
 */
 class Protect extends Middleware
 {
     public static function execute(): bool
     {
        if(!SessionEditor::getAttribute(SessionEditor::AUTHENTICATED))
        {
            SessionEditor::setAttribute(SessionEditor::ALERTS, ["You must be logged in to access this page."]);
            header("Location: /login");
            return false;
        }
        
        return true;
     }
 }

?>