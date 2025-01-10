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

use controllers\ProjectController;
use controllers\AuthController;
use controllers\TaskController;
use controllers\MemberController;
use controllers\RolesController;
use config\TokenManager;

// Member management routes
$router->addRoute('POST', 'api/users', function() {
    $controller = new MemberController();
    $controller->handleRequest();
});

// Role management routes
$router->addRoute('POST', 'roles', function() {
    $controller = new RolesController();
    $controller->handleRequest();
});

// Authentication Checkpoint
function checkAuthentication() {
    // List of routes that do not require authentication
    $publicRoutes = [
        '',         // home
        'auth',     // login/register page
        'handelauth' // authentication handler
    ];

    // Get current route
    $currentRoute = $_GET['route'] ?? '';

    // Check if route requires authentication
    if (!in_array($currentRoute, $publicRoutes)) {
        // Check if user is logged in via session
        if (!isset($_SESSION['user'])) {
            // If not in session, check for token
            $token = TokenManager::getTokenFromHeader();

            if (!$token) {
                // No token found, redirect to login
                header('Location: /auth');
                exit;
            }

            try {
                // Validate token
                $tokenData = TokenManager::validateToken($token);

                if (!$tokenData) {
                    // Invalid token
                    unset($_SESSION['token']);
                    header('Location: /auth');
                    exit;
                }

                // Store user data in session
                $_SESSION['user'] = $tokenData;
            } catch (Exception $e) {
                // Token validation error
                header('Location: /auth');
                exit;
            }
        }
    }
}

// Call authentication checkpoint before dispatching routes
checkAuthentication();

// Dispatch the request
$router->dispatch();
