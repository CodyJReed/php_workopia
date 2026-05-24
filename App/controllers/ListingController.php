<?php

namespace App\Controllers;

use Framework\Database;

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

        if(!$listing) {
            ErrorController::notFound('Listing not found.');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }
}
