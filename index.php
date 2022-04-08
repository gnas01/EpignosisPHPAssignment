<?php

session_start();

require_once "router.php";

require_once "./controllers/authController.php";
require_once "./controllers/adminController.php";

require_once "./middlewares/protect.php";
require_once "./middlewares/protectAdmin.php";

$router = new Router();

$router->get('/', function() 
{
    header('Location: /login');
}, false);

$router->get('/login', AuthController::class . '::viewLogin');

$router->post('/userLogin', AuthController::class . '::loginUserHandler');
$router->get('/logout', AuthController::class . '::logoutUserHandler');


$router->get('/admin', AdminController::class . '::viewAdmin', fn() => protectAdmin());
$router->get('/createUser', AdminController::class . '::viewCreateUser', fn () => protectAdmin());
$router->get('/updateUser', AdminController::class . '::viewUpdateUser', fn () => protectAdmin());

$router->post('/createUser', AdminController::class . '::createUserHandler', fn () => protectAdmin());
$router->post('/updateUser', AdminController::class . '::updateUserHandler', fn () => protectAdmin());


$router->addNotFoundHandler(function() 
{
    include_once "views/404.phtml";
});

$router->run();

?>
