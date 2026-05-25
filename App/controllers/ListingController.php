<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{
    protected $db;
    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show all listings
     * 
     * @return void
     */
    public function index()
    {

        $listings = $this->db->query('SELECT * FROM listings LIMIT 6')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    /**
     * Show listing creation page
     * 
     * @return void
     */
    public function create()
    {
        loadView('listings/create');
    }

    /**
     * Show target listing by ID
     * 
     * @return void
     */
    public function show($params)
    {
        $id = $params['id'] ?? '';

        if ($id) {
            $params = [
                'id' => $id
            ];

            $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();
        }

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in DB
     * 
     * @return void
     */
    public function store()
    {
        $allowedFields = [
            'title',
            'description',
            'salary',
            'tags',
            'phone',
            'email',
            'requirements',
            'benefits'
        ];
        //Filter two arrays by keys, returning new Array...
        /// When comparing an Associative array against a traditional 'array_flip'...
        // ..can be used to flip index => values to bey 'keys'
        $formData = array_intersect_key($_POST, array_flip($allowedFields));
        // TODO replace hardcode with dynamic user $value
        $formData['user_id'] = 1;
        // Sanitize data against html
        $formData = array_map('sanitize', $formData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];

        $errors = [];
        //Loop through required fields
        foreach ($requiredFields as $field) {
            //if a form field matching a required fields is empty or not a string...
            if (empty($formData[$field]) || !Validation::string($formData[$field])) {
                // Add field to $errors Array
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if (!empty($errors)) {
            // Reload view with errors
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $formData
            ]);
        } else {
            // Submit formData
            echo 'Submitted.';
        }
    }
}
