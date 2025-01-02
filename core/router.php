<?php
namespace core;

class Router {
    private $routes = [];
    private $notFoundHandler;

    public function __construct() {
        // Default 404 handler
        $this->notFoundHandler = function() {
            http_response_code(404);
            echo "404 - Page Not Found";
            exit;
        };
    }

    /**
     * Add a route to the routing table
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $path Route path
     * @param callable $handler Route handler
     */
    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
          
            'path' => $path,
            'handler' => $handler
        ];
        return $this;
    }

    /**
     * Dispatch the current request
     */
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Remove query string and trim leading/trailing slashes
        $requestUri = trim(strtok($requestUri, '?'), '/');


        foreach ($this->routes as $route) {
            if ( $route['path'] === $requestUri) {
                return call_user_func($route['handler']);
            }
        }

        // No route found, call 404 handler
        call_user_func($this->notFoundHandler);
    }
}

// Helper function to create router instance
function router() {
    return new Router();
}