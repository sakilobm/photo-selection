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
            'name' => $p['name'],
            'category' => strtolower($p['category']),
            'url' => $p['url'],
            'selected' => (int)$p['selected'] === 1,
            'deleted' => (int)$p['deleted'] === 1
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
