<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Iniciar sessão de forma segura se já não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
