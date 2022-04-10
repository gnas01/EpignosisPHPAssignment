<?php

namespace middleware;

use core\SessionEditor;

/**
 * Middleware used to restrict routes only to authenticated users
 */

include_once "./core/sessionEditor.php";

/**
 * Middleware used to restrict routes only to authenticated users
 * Not authenticated users will be redirected to the login page
 */
function protect()
{
    if(!SessionEditor::getAttribute(SessionEditor::AUTHENTICATED))
    {
        header("Location: /login");
    }
}


?>