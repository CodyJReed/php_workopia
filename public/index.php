<?php
// Require PSR-4 autoloader
require __DIR__ . '/../vendor/autoload.php';

// Add namespace
use Framework\Router;
use Framework\Session;
// Start session
Session::start();

require '../helpers.php';

// Create router instance
$router = new Router();
// Retrieve routes
$routes = require basePath('routes.php');
// Get current uri and http method.. stripping out query params
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route reques(s)
$router->route($uri);
