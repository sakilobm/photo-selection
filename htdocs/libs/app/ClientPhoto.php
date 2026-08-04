<?php

namespace App;

use Aether\Traits\SQLGetterSetter;
use Aether\Database;

class ClientPhoto
{
    use SQLGetterSetter;

    public int $id;
    public string $table = 'client_photos';
    public $conn;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->conn = Database::getConnection();
    }

    /**
     * Add a photo to a client portal.
     */
    public static function add(array $data): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `client_photos` (`portal_id`, `filename`, `category`, `size`, `thumb_url`, `selection_status`, `notes`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $data['portal_id'],
            $data['filename'],
            $data['category'] ?? 'CANDID',
            $data['size'] ?? '3.5 MB',
            $data['thumb_url'] ?? null,
            $data['selection_status'] ?? 'PENDING',
            $data['notes'] ?? null
        ]);

        if ($success) {
            $photo = new self((int)$db->lastInsertId());
            // Update portal total photos count
            $portal = new ClientPortal((int)$data['portal_id']);
            $portal->updatePhotoCount();
            return $photo;
        }
        return null;
    }
}
