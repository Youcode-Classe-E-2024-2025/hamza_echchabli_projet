<?php
session_start();

// Autoload classes
require_once 'vendor/autoload.php';

use config\TokenManager;
use controllers\UserController;
use controllers\ProjectController;

// Define routes
$routes = [
    '/' => 'home',
    '/auth' => 'auth',
    '/myproject' => 'myproject',
    '/CRUDProject' => 'project',
    // Add more routes as needed
];

// Paths that don't require authentication
$publicRoutes = [
    '/',
    '/auth',
    '/login',
    '/register'
];

// Get current path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check if authentication is required
$requiresAuth = !in_array($path, $publicRoutes);

// Global Authorization Check
if ($requiresAuth) {
    try {
        // Get token from header
        $token = TokenManager::getTokenFromHeader();

        // If no token is present, redirect to login
        if (!$token) {
            header('Location: /auth');
            exit;
        }

        // Validate token
        $tokenData = TokenManager::validateToken($token);

        // If token is invalid, redirect to login
        if (!$tokenData) {
            header('Location: /auth');
            exit;
        }

        // Store user data in session for further use
        $_SESSION['user'] = $tokenData;
    } catch (Exception $e) {
        // Handle any unexpected errors
        header('Location: /auth');
        exit;
    }
}

// Route handling
try {
    switch ($path) {
        case '/':
            // Home page logic
            require_once 'views/home.php';
            break;

        case '/auth':
            $userController = new UserController();
            $userController->handleRequests();
            break;

        case '/CRUDProject':
            $projectController = new ProjectController();
            $projectController->handleRequest();
            break;

        default:
            // 404 Not Found
            http_response_code(404);
            echo "Page not found";
            break;
    }
} catch (Exception $e) {
    // Global error handling
    http_response_code(500);
    echo "An error occurred: " . $e->getMessage();
}
