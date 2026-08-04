<?php

/**
 * Save Package API Controller (Admin Only)
 * ========================================
 * POST /api/admin/save_package
 */

use App\Package;
use Aether\Database;

$save_package = function () {
    $this->requireAuth();

    $data = array_merge($this->_request, $this->getJsonPayload());
    $pkgId = $data['id'] ?? null;

    if (!$pkgId) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Package ID is required.'
        ]), 400);
    }

    $name = $data['name'] ?? '';
    $price = (int)($data['price'] ?? 0);
    $badge = $data['badge'] ?? '';
    $desc = $data['desc'] ?? '';
    $features = $data['features'] ?? [];

    $db = Database::getConnection();

    $stmt = $db->prepare("SELECT `id` FROM `packages` WHERE `id` = ?");
    $stmt->execute([$pkgId]);
    $exists = $stmt->fetch();

    $featuresJson = json_encode($features);

    if ($exists) {
        $stmt = $db->prepare("UPDATE `packages` SET `name` = ?, `price` = ?, `badge` = ?, `desc` = ?, `features` = ? WHERE `id` = ?");
        $success = $stmt->execute([$name, $price, $badge, $desc, $featuresJson, $pkgId]);
    } else {
        $stmt = $db->prepare("INSERT INTO `packages` (`id`, `name`, `price`, `badge`, `desc`, `features`) VALUES (?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$pkgId, $name, $price, $badge, $desc, $featuresJson]);
    }

    if ($success) {
        $this->response($this->json([
            'success' => true,
            'message' => 'Package ' . $name . ' pricing saved successfully.'
        ]), 200);
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to save package settings.'
        ]), 500);
    }
};
