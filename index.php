<?php

require_once "router.php";

$router = new Router();

$router->get('/', function() 
{
    header('Location: /login');
});

$router->get('/login', function() 
{
    include_once "views/signin.phtml";
});

$router->addNotFoundHandler(function() 
{
    include_once "views/404.phtml";
});

$router->run();

?>
