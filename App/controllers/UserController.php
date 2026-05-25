<?php

namespace App\Controllers;

use Framework\Database;

class UserController
{
    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show login page
     * 
     * @return void
     */
    public function login()
    {

        loadView('users/login');
    }

    /**
     * Register a new user
     * @param mixed $params
     * 
     * @return void
     */
    public function create($params)
    {

        loadView('users/create');
    }
}
