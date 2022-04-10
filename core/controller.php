<?php

namespace core;

/**
 * Base class for all controllers. Responsible for
 * the way an actor interacts with the application.
 *  The controller is the bridge between the model and the view.
 */
abstract class Controller
{

    /**
     * Takes a view component and renders it. 
     * Optionally, data can be passed to the view.
     * @param string $view The name of the view to render.
     * @param array $data Optional data to be passed into the view.
     */
    protected function renderView(string $view, array $data = []): void
    {
        extract($data);
        include_once "views/$view.phtml";

    }

    /**
     * Redirects the actor to a new page.
     * @param string $path The url to redirect to.
     */
    protected function redirect(string $path): void
    {
        header("Location: $path");
    }

}

?>