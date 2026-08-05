<?php

/**
 * Get Client Photos API Controller
 * ================================
 * GET /api/photos/get_client_photos
 */

use App\ClientPortal;
use Aether\Session;

$get_client_photos = function () {
    $clientId = Session::get('client_id');
    if (!$clientId) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Unauthorized client session.'
        ]), 401);
    }

    $portal = new ClientPortal($clientId);
    $photos = $portal->getPhotos();

    // Map fields to match JavaScript names
    $mappedPhotos = array_map(function ($p) {
        return [
            'id' => (int)$p['id'],
            'name' => $p['filename'] ?? '',
            'category' => strtolower($p['category'] ?? 'candid'),
            'url' => $p['thumb_url'] ?? '',
            'selected' => (strtoupper($p['selection_status'] ?? '') === 'APPROVED'),
            'deleted' => false
        ];
    }, $photos);

    $this->response($this->json([
        'success' => true,
        'photos' => $mappedPhotos,
        'portal' => [
            'client_name' => $portal->getClientName(),
            'email' => $portal->getEmail(),
            'event_date' => $portal->getEventDate(),
            'max_selection' => (int)$portal->getMaxSelection(),
            'status' => $portal->getStatus(),
            'code' => $portal->getCode()
        ]
    ]), 200);
};
