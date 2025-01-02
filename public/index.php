<?php

// Require autoloader and core classes
require_once  '../core/autoloader.php';
require_once '../core/router.php';


// Start the session
session_start();

// Create router instance
$router = new core\Router();

///handelLogin

// Define authentication routes
$router->addRoute('POST', 'auth', function() {
        require_once '../views/auth.php';
    });


    $router->addRoute('POST', 'handelauth', function() {
        $userController = new controllers\UserController();
        $userController->handleRequests();
    });

    
   

// Dispatch the request
$router->dispatch();
