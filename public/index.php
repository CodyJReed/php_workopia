<?php
require '../helpers.php';
require basePath('Router.php');
require basePath('Database.php');

// Create router instance
$router = new Router();
// Retrieve routes
$routes = require basePath('routes.php');
// Get current uri and http method.. stripping out query params
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
// Route reques(s)
$router->route($uri, $method);
