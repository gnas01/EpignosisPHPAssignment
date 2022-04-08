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

$router->post('/userLogin', AuthController::class . '::loginUserHandler');

$router->get('/admin', AdminController::class . '::viewAdmin');
$router->get('/createUser', AdminController::class . '::viewCreateUser');
$router->get('/updateUser', AdminController::class . '::viewUpdateUser');

$router->post('/createUser', AdminController::class . '::createUserHandler');
$router->post('/updateUser', AdminController::class . '::updateUserHandler');


$router->addNotFoundHandler(function() 
{
    include_once "views/404.phtml";
});

$router->run();

?>
