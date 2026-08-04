<?php

/**
 * Toggle Portal Flag API Controller (Admin Only)
 * ============================================
 * POST /api/admin/toggle_flag
 */

use App\ClientPortal;
use Aether\Database;

$toggle_flag = function () {
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

    $flagged = (int)$portal->getFlagged() === 1 ? 0 : 1;
    $status = $flagged ? 'Completed' : 'Pending';

    $stmt = $db->prepare("UPDATE `client_portals` SET `flagged` = ?, `status` = ? WHERE `id` = ?");
    $success = $stmt->execute([$flagged, $status, $portal->id]);

    if ($success) {
        $this->response($this->json([
            'success' => true,
            'flagged' => $flagged === 1,
            'status' => $status
        ]), 200);
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to toggle portal completion flag.'
        ]), 500);
    }
};
