<?php

/**
 * Client Login API Controller
 * ===========================
 * POST /api/auth/client_login
 * Parameters: code (Event Passcode)
 */

use App\ClientPortal;
use Aether\Session;

$client_login = function () {
    // Read request parameters and JSON body
    $data = array_merge($this->_request, $this->getJsonPayload());
    $code = $data['code'] ?? null;
    $email = $data['email'] ?? null;

    if ($code && $email) {
        $portal = ClientPortal::findByCode($code);
        if ($portal) {
            // Check if blocked
            if ((int)$portal->getBlocked() === 1) {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'Your portal access has been revoked. Please contact OBM Studio.'
                ]), 403);
            }

            // Verify email matches the portal email address case-insensitively
            if (strcasecmp(trim($portal->getEmail()), trim($email)) !== 0) {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'The email address does not match this passcode.'
                ]), 401);
            }

            // Set Client session parameters
            Session::set('client_id', $portal->id);
            Session::set('client_email', $portal->getEmail());
            Session::set('client_name', $portal->getClientName());
            Session::set('client_code', $code);

            $this->response($this->json([
                'success' => true,
                'message' => 'Unlocked ' . $portal->getClientName() . '\'s gallery!',
                'email' => $portal->getEmail(),
                'name' => $portal->getClientName()
            ]), 200);
        } else {
            $this->response($this->json([
                'success' => false,
                'message' => 'Invalid passcode. Please try again.'
            ]), 401);
        }
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Both email address and passcode are required.'
        ]), 400);
    }
};
