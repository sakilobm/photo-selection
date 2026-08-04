<?php

/**
 * Upload Photo API Controller (Admin Only)
 * ========================================
 * POST /api/admin/upload_photos
 * Parameters: code (Portal Passcode), category, file (Uploaded File)
 */

use App\ClientPortal;
use Aether\Database;

$upload_photos = function () {
    $this->requireAuth();

    $code = $_POST['code'] ?? null;
    $category = $_POST['category'] ?? 'candid';

    if (!$code) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Portal passcode is required.'
        ]), 400);
    }

    $portal = ClientPortal::findByCode($code);
    if (!$portal) {
        $this->response($this->json([
            'success' => false,
            'message' => 'Client portal not found.'
        ]), 404);
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $this->response($this->json([
            'success' => false,
            'message' => 'No valid file received.'
        ]), 400);
    }

    $file = $_FILES['file'];

    // Ensure local uploads directory exists
    $uploadDir = HTDOCS_ROOT . '/uploads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Generate safe clean unique filename
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($file['name']));
    $targetPath = $uploadDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $db = Database::getConnection();
        
        $url = 'uploads/' . $filename;
        $stmt = $db->prepare("INSERT INTO `client_photos` (`portal_id`, `name`, `category`, `url`, `selected`, `deleted`) VALUES (?, ?, ?, ?, 0, 0)");
        $success = $stmt->execute([$portal->id, basename($file['name']), $category, $url]);

        if ($success) {
            $portal->updatePhotoCount();

            $this->response($this->json([
                'success' => true,
                'message' => 'Photo uploaded and registered successfully.',
                'photo' => [
                    'name' => basename($file['name']),
                    'category' => $category,
                    'url' => $url
                ]
            ]), 200);
        } else {
            @unlink($targetPath); // Cleanup uploaded file if SQL insert fails
            $this->response($this->json([
                'success' => false,
                'message' => 'Failed to write photo reference to database.'
            ]), 500);
        }
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to save file to disk.'
        ]), 500);
    }
};
