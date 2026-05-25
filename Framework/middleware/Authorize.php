<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize {
    /**
     * Check is user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated() {
        return Session::has('user');
    }
    /**
     * Handle user request
     * 
     * @param string $role
     * 
     * @return bool
     */
    public function handle($role) {
        if($role === 'guest' && $this->isAuthenticated()) {
            redirect('/');
        } else if ($role === 'auth' && !$this->isAuthenticated()) {
            redirect('/auth/login');
        }
    }
}