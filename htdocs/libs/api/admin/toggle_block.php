<?php

/**
 * Toggle Portal Block API Controller (Admin Only)
 * =============================================
 * POST /api/admin/toggle_block
 */

use App\ClientPortal;
use Aether\Database;

$toggle_block = function () {
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

    $blocked = (int)$portal->getBlocked() === 1 ? 0 : 1;
    $status = $blocked ? 'Blocked' : ($portal->getFlagged() ? 'Completed' : 'Pending');

    $stmt = $db->prepare("UPDATE `client_portals` SET `blocked` = ?, `status` = ? WHERE `id` = ?");
    $success = $stmt->execute([$blocked, $status, $portal->id]);

    if ($success) {
        $this->response($this->json([
            'success' => true,
            'blocked' => $blocked === 1,
            'status' => $status
        ]), 200);
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to toggle portal block status.'
        ]), 500);
    }
};
