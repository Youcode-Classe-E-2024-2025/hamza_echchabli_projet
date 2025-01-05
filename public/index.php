<?php

// Require autoloader and core classes
require_once  '../core/autoloader.php';
require_once '../core/router.php';


// Start the session
session_start();

// Create router instance
$router = new core\Router();

// Define routes
$router->addRoute('GET', '', function() {
    require_once '../views/home.php';
});

$router->addRoute('GET', 'auth', function() {
    require_once '../views/auth.php';
});

$router->addRoute('GET', 'kanban', function() {
    require_once '../views/kanbanDisplay.php';
});   

$router->addRoute('GET', 'myproject', function() {
    require_once '../views/myproject.php';
});

$router->addRoute('GET', 'mytasks', function() {
    require_once '../views/myTasks.php';
});

$router->addRoute('POST', 'handelauth', function() {
    $userController = new controllers\UserController();
    $userController->handleRequests();
});

$router->addRoute('POST', 'CRUDProject', function() {
    $projectController = new controllers\ProjectController();
    $projectController->handleRequest();
});

$router->addRoute('POST', 'CRUDTask', function() {
    $taskController = new controllers\TaskController();
    $taskController->handleRequest();
});

$router->addRoute('POST', 'CRUDAssing', function() {
    $assingController = new controllers\AssignTasksController();
    $assingController->handleRequest();
});

$router->addRoute('GET', 'logout', function() {
    require_once '../controllers/logout.php';
});

// Dispatch the request
$router->dispatch();
