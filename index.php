<?php

session_start();

require_once "router.php";

require_once "./controllers/authController.php";
require_once "./controllers/adminController.php";

$router = new Router();

$router->get('/', function() 
{
    header('Location: /login');
});

$router->get('/login', AuthController::class . '::viewLogin');
$router->get('/admin', AdminController::class . '::viewAdmin');

$router->post('/userLogin', AuthController::class . '::loginUserHandler');


$router->addNotFoundHandler(function() 
{
    include_once "views/404.phtml";
});

$router->run();

?>
