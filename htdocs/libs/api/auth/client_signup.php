<?php

/**
 * Client Portal Signup API Controller
 * ===================================
 * POST /api/auth/client_signup
 * Parameters: name, email, code
 */

use App\ClientPortal;
use Aether\Database;

$client_signup = function () {
    $data = array_merge($this->_request, $this->getJsonPayload());

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $code = trim($data['code'] ?? '');

    if (empty($name) || empty($email) || empty($code)) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Your name, email address, and desired passcode are required.'
        ]), 400);
    }

    // Passcode format check
    if (strlen($code) < 4) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Passcode must be at least 4 characters long.'
        ]), 400);
    }

    try {
        // 1. Passcode uniqueness check
        $existing = ClientPortal::findByCode($code);
        if ($existing) {
            $this->response($this->json([
                'success' => false,
                'message' => 'This passcode is already taken. Please choose a unique key.'
            ]), 409);
        }

        // 2. Email duplication check (optional warning or constraint)
        $existingEmail = ClientPortal::findByEmail($email);
        if ($existingEmail) {
            $this->response($this->json([
                'success' => false,
                'message' => 'A portal workspace has already been requested for this email address.'
            ]), 409);
        }

        // 3. Create the portal record
        $portal = ClientPortal::create([
            'code' => $code,
            'client_name' => $name,
            'email' => $email,
            'event_date' => date('Y-m-d', strtotime('+3 months')), // Default future date placeholder
            'max_selection' => 100,
            'status' => 'Pending',
            'blocked' => 0,
            'flagged' => 0
        ]);

        if ($portal) {
            $this->response($this->json([
                'success' => true,
                'message' => 'Secure portal workspace initialized successfully! Please contact OBM Studio to upload your photos.'
            ]), 201);
        } else {
            $this->response($this->json([
                'success' => false,
                'message' => 'Failed to initialize client portal database record.'
            ]), 500);
        }

    } catch (\Exception $e) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]), 500);
    }
};
