<?php

use core\Router;
use core\Database;
use Middleware\Protect;
use Middleware\ProtectAdmin;

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


$router->get('/admin', AdminController::class . '::viewAdmin', fn() => ProtectAdmin::execute());
$router->get('/createUser', AdminController::class . '::viewCreateUser', fn () => ProtectAdmin::execute());
$router->get('/updateUser', AdminController::class . '::viewUpdateUser', fn () => ProtectAdmin::execute());

$router->post('/createUser', AdminController::class . '::createUserHandler', fn () => ProtectAdmin::execute());
$router->post('/updateUser', AdminController::class . '::updateUserHandler', fn () => ProtectAdmin::execute());

$router->get('/home', UserController::class . '::viewHome', fn () => Protect::execute());
$router->get('/submitRequest', UserController::class . '::viewSubmitRequest', fn () => Protect::execute());

$router->post('/submitRequest', UserController::class . '::submitRequestHandler', fn () => Protect::execute());

$router->get('/updateSubmission', AdminController::class . '::updateSubmissionHandler');


$router->addNotFoundHandler(function() 
{
    include_once "views/404.phtml";
});

$router->run();

?>
