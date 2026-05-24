<?php
require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

// Create custom autoloader
// spl_autoload_register(function ($class) {
//     // create a path matching 'class' name found with Framework dir
//     $path = basePath('Framework/' . $class . '.php');
//     // If the 'path' exists, require it
//     if (file_exists($path)) {
//         require $path;
//     }
// });

// Create router instance
$router = new Router();
// Retrieve routes
$routes = require basePath('routes.php');
// Get current uri and http method.. stripping out query params
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
// Route reques(s)
$router->route($uri, $method);
