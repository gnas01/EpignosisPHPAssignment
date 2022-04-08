<?php

include_once "./sessionEditor.php";

function protect()
{
    if(!SessionEditor::getAttribute(SessionEditor::AUTHENTICATED))
    {
        header("Location: /login");
    }
}


?>