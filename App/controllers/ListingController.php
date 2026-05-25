<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

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

        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC LIMIT 6')->fetchAll();

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
            'city',
            'state',
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
        $formData['user_id'] = Session::get('user')['id'];
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
            exit;
        } else {
            // Submit formData
            $fields = [];
            $values = [];

            foreach ($formData as $field => $value) {
                $fields[] = $field;
                if ($value === '') {
                    $formData[$field] = null;
                }
                $values[] = ':' . $field;
            }

            $fields = implode(', ', $fields);
            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            $this->db->query($query, $formData);
            redirect('/listings');
        }
    }

    /**
     * Delete listing by id
     * 
     * @param array $params
     * 
     * @return void
     */
    public function destroy($params)
    {
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }
        // Check Auth
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing.');
            redirect('/listings/' . $listing->id);
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);
        // Set flash message
        Session::setFlashMessage('success_message', 'Listing deleted successfully!');
        redirect('/listings');
    }

    /**
     * Show target listing edit form
     * 
     * @return void
     */
    public function edit($params)
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

        // Check Auth
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to edit this listing.');
            redirect('/listings/' . $listing->id);
        }

        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing
     * 
     * @param array $params
     * 
     * @return void
     */
    public function update($params)
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

        // Check Auth
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to edit this listing.');
            redirect('/listings/' . $listing->id);
        }

        // The following borrows heavily from the 'store' method
        // See method's comments for additional insights
        $allowedFields = [
            'title',
            'description',
            'salary',
            'tags',
            'city',
            'state',
            'phone',
            'email',
            'requirements',
            'benefits'
        ];

        $formData = array_intersect_key($_POST, array_flip($allowedFields));
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
            exit;
        } else {
            // Submit formData
            $fields = [];

            foreach (array_keys($formData) as $field) {
                $fields[] = "{$field} = :{$field}";
            }

            $fields = implode(', ', $fields);

            $query = "UPDATE listings SET $fields WHERE id = :id";
            $formData['id'] = $id;
            $this->db->query($query, $formData);

            Session::setFlashMessage('success_message', 'Listing updated!');

            redirect('/listings');
        }
    }

    /**
     * Search listings by keywords/location
     * 
     * @return void
     */
    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%"
        ];

        $query = "SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location)";

        $listings = $this->db->query($query, $params)->fetchAll();
        
        loadView('/listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }
}
