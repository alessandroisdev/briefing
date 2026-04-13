<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Initialize Database connection
require_once __DIR__ . '/../app/Core/Database.php';

// Create Router instance
$router = new \Bramus\Router\Router();

// Load routes
require_once __DIR__ . '/../routes/web.php';

// Run the router
$router->run();
