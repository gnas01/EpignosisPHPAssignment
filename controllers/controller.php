<?php

class Controller
{
    protected function renderView(string $view, array $data = [])
    {
        extract($data);
        include_once "views/$view.phtml";
    }

    protected function redirect(string $path)
    {
        header("Location: $path");
    }

}

?>