<?php

/**
 * Client Logout API Controller
 * ============================
 * POST /api/auth/client_logout
 */

use Aether\Session;

$client_logout = function () {
    // Delete all client portal session variables
    Session::delete('client_id');
    Session::delete('client_email');
    Session::delete('client_name');
    Session::delete('client_code');

    $this->response($this->json([
        'success' => true,
        'message' => 'Logged out of client workspace'
    ]), 200);
};
