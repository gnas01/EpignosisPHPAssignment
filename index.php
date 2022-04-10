<?php

use core\Router;

/**
 * Starting point of the application.
 * This file is responsible for loading the routes
 */

session_start();

require_once "./core/router.php";

require_once "./controllers/authController.php";
require_once "./controllers/adminController.php";
require_once "./controllers/userController.php";

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


$router->get('/admin', AdminController::class . '::viewAdmin', fn() => middleware\protectAdmin());
$router->get('/createUser', AdminController::class . '::viewCreateUser', fn () => middleware\protectAdmin());
$router->get('/updateUser', AdminController::class . '::viewUpdateUser', fn () => middleware\protectAdmin());

$router->post('/createUser', AdminController::class . '::createUserHandler', fn () => middleware\protectAdmin());
$router->post('/updateUser', AdminController::class . '::updateUserHandler', fn () => middleware\protectAdmin());

$router->get('/home', UserController::class . '::viewHome', fn () => middleware\protect());
$router->get('/submitRequest', UserController::class . '::viewSubmitRequest', fn () => middleware\protect());

$router->post('/submitRequest', UserController::class . '::submitRequestHandler', fn () => middleware\protect());

$router->get('/updateSubmission', AdminController::class . '::updateSubmissionHandler');


$router->addNotFoundHandler(function() 
{
    include_once "views/404.phtml";
});

$router->run();

?>
