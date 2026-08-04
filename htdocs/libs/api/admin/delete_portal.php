<?php

/**
 * Delete Portal API Controller (Admin Only)
 * ========================================
 * POST /api/admin/delete_portal
 */

use App\ClientPortal;
use Aether\Database;

$delete_portal = function () {
    $this->requireAuth();

    $data = array_merge($this->_request, $this->getJsonPayload());
    $code = $data['code'] ?? null;

    if (!$code) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Passcode code is required.'
        ]), 400);
    }

    $portal = ClientPortal::findByCode($code);
    if (!$portal) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Portal not found.'
        ]), 404);
    }

    $db = Database::getConnection();

    $stmt = $db->prepare("DELETE FROM `client_portals` WHERE `id` = ?");
    $success = $stmt->execute([$portal->id]);

    if ($success) {
        $this->response($this->json([
            'success' => true,
            'message' => 'Portal and associated assets deleted successfully.'
        ]), 200);
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to delete client portal.'
        ]), 500);
    }
};
