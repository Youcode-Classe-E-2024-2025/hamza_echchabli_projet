<?php

// namespace Core;

class Autoloader {
    // Base directory for the namespace
    private $baseDir;

    public function __construct() {
        // Set base directory to the project root
        $this->baseDir = dirname(__DIR__) . '/';
    }

    // Autoloader method
    public function load($className) {
        // Replace namespace separators with directory separators
        $className = str_replace('\\', '/', $className);
        
        // Construct the full path to the file
        $file = $this->baseDir . $className . '.php';
        
        // If the file exists, include it
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        
        return false;
    }

    // Register the autoloader
    public function register() {
        spl_autoload_register([$this, 'load']);
    }
}

// Instantiate and register the autoloader
$autoloader = new Autoloader();
$autoloader->register();