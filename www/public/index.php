<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Impedir que avisos de Deprecated de bibliotecas de terceiros (ex: brick/math) quebrem os headers do router
error_reporting(E_ALL & ~E_DEPRECATED);

// Require Helpers directly to ensure they load extremely early before Router
require_once __DIR__ . '/../app/Core/Helpers.php';

// Iniciar sessão de forma segura e encapsulada
session();

// Capturar e guardar o Request global do Illuminate/Http
request();

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
