<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

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
     * 
     * @return void
     */
    public function create()
    {

        loadView('users/create');
    }

    /**
     * Store user in DB
     * 
     * @return void
     */
    public function store()
    {
        $formData = $_POST;

        $formData = array_map('sanitize', $formData);

        $errors = [];

        // Validation
        if (!Validation::email($formData['email'])) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (!Validation::string($formData['name'], 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters.';
        }
        if (!Validation::string($formData['password'], 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }
        if (!Validation::match($formData['password'], $formData['password_confirmation'])) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        if (!empty($errors)) {
            loadView('users/create', [
                'errors' => $errors,
                'user' => $formData
            ]);
            exit;
        }

        // Check if email exists in DB
        $params = [
            'email' => $formData['email']
        ];

        $match = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();

        if ($match) {
            $errors['email'] = 'Email is already taken.';

            loadView('users/create', [
                'errors' => $errors,
                'user' => $formData
            ]);
            exit;
        }

        // Create user account
        $params = [
            'name' => $formData['name'],
            'email' => $formData['email'],
            'city' => $formData['city'],
            'state' => $formData['state'],
            'password' => password_hash($formData['password'], PASSWORD_DEFAULT)
        ];

        $this->db->query('INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)', $params);
        // Get new user id
        $userId = $this->db->conn->lastInsertId();
        // Create user session
        Session::set('user', [
            'id' => $userId,
            'name' => $formData['name'],
            'email' => $formData['email'],
            'city' => $formData['city'],
            'state' => $formData['state'],
        ]);
        // Add success message;
        Session::setFlashMessage('success_message', 'User successfully registered!');

        redirect('/');
    }

    /**
     * Logout a user session and remove session cookie
     * 
     * @return void
     */
    public function logout()
    {
        Session::clearAll();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 86400, $params['path'], $params['domain']);

        redirect('/');
    }

    /**
     * Authenticate a user
     * 
     * @return void
     */
    public function auth()
    {
        $formData = $_POST;

        $formData = array_map('sanitize', $formData);

        $errors = [];
        // Validation
        if (!Validation::email($formData['email'])) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (!Validation::string($formData['password'], 6, 50)) {
            $errors['password'] = 'Password must be at least 6 characters.';
        }

        if (!empty($errors)) {
            loadView('users/login', [
                'errors' => $errors,
                'user' => $formData
            ]);
            exit;
        }

        // Check if user exists in DB
        $params = [
            'email' => $formData['email']
        ];

        $match = $this->db->query('SELECT * FROM users WHERE email = :email', $params)->fetch();
        // If no match is found or password isn't correct...
        if (!$match || !password_verify($formData['password'], $match->password)) {
            $errors['email'] = 'Incorrect email or password.';

            loadView('users/login', [
                'errors' => $errors,
                'user' => $formData
            ]);
            exit;
        }
        // Set user session
        Session::set('user', [
            'id' => $match->id,
            'name' => $match->name,
            'email' => $match->email,
            'city' => $match->city,
            'state' => $match->state,
        ]);

        redirect('/');
    }
}
