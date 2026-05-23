<?php
require '../helpers.php';
require basePath('Router.php');
require basePath('Database.php');

// Create router instance
$router = new Router();
// Retrieve routes
$routes = require basePath('routes.php');
// Get current uri and http method
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
// Route reques(s)
$router->route($uri, $method);
