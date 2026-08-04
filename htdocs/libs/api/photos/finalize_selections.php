<?php

/**
 * Finalize Selections API Controller
 * ==================================
 * POST /api/photos/finalize_selections
 * Parameters: selected_ids (Array of photo IDs)
 */

use App\ClientPortal;
use Aether\Session;
use Aether\Database;

$finalize_selections = function () {
    $clientId = Session::get('client_id');
    if (!$clientId) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Unauthorized client session.'
        ]), 401);
    }

    $data = array_merge($this->_request, $this->getJsonPayload());
    $selectedIds = $data['selected_ids'] ?? [];

    if (!is_array($selectedIds)) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Invalid selections payload.'
        ]), 400);
    }

    $db = Database::getConnection();

    try {
        $db->beginTransaction();

        // 1. Reset all photos of this portal to unselected
        $stmt = $db->prepare("UPDATE `client_photos` SET `selected` = 0 WHERE `portal_id` = ?");
        $stmt->execute([$clientId]);

        // 2. Mark the selected photos as selected
        if (!empty($selectedIds)) {
            // Clean/convert each id to integer
            $cleanIds = array_map('intval', $selectedIds);
            $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
            $stmt = $db->prepare("UPDATE `client_photos` SET `selected` = 1 WHERE `portal_id` = ? AND `id` IN ($placeholders)");
            $stmt->execute(array_merge([$clientId], $cleanIds));
        }

        // 3. Mark the portal status as "Completed"
        $stmt = $db->prepare("UPDATE `client_portals` SET `status` = 'Completed', `flagged` = 1 WHERE `id` = ?");
        $stmt->execute([$clientId]);

        $db->commit();

        $this->response($this->json([
            'success' => true,
            'message' => 'Selections saved and locked successfully.'
        ]), 200);

    } catch (\Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to save selections: ' . $e->getMessage()
        ]), 500);
    }
};
