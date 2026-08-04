<?php

/**
 * Create Portal API Controller (Admin Only)
 * ========================================
 * POST /api/admin/create_portal
 */

use App\ClientPortal;

$create_portal = function () {
    $this->requireAuth(); // Restrict to authenticated admins

    $data = array_merge($this->_request, $this->getJsonPayload());

    $code = $data['code'] ?? null;
    $client_name = $data['client_name'] ?? null;
    $email = $data['email'] ?? null;
    $event_date = $data['event_date'] ?? null;
    $max_selection = $data['max_selection'] ?? 100;

    if (!$code || !$client_name || !$email) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Passcode, Client Name, and Email are required.'
        ]), 400);
    }

    // Passcode uniqueness check
    $existing = ClientPortal::findByCode($code);
    if ($existing) {
        $this->response($this->json([
            'success' => false,
            'message' => 'This passcode is already allocated to another portal.'
        ]), 409);
    }

    $portal = ClientPortal::create([
        'code' => $code,
        'client_name' => $client_name,
        'email' => $email,
        'event_date' => $event_date,
        'max_selection' => $max_selection,
        'status' => 'Pending',
        'blocked' => 0,
        'flagged' => 0
    ]);

    if ($portal) {
        $this->response($this->json([
            'success' => true,
            'message' => 'Portal created successfully for ' . $client_name
        ]), 201);
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to create client portal in database.'
        ]), 500);
    }
};
