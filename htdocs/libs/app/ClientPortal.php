<?php

namespace App;

use Aether\Traits\SQLGetterSetter;
use Aether\Database;

class ClientPortal
{
    use SQLGetterSetter;

    public int $id;
    public string $table = 'client_portals';
    public $conn;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->conn = Database::getConnection();
    }

    /**
     * Find a portal by its passcode.
     */
    public static function findByCode(string $code): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT `id` FROM `client_portals` WHERE BINARY `code` = ? LIMIT 1");
        $stmt->execute([$code]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? new self((int)$row['id']) : null;
    }

    /**
     * Find a portal by its email.
     */
    public static function findByEmail(string $email): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT `id` FROM `client_portals` WHERE `email` = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? new self((int)$row['id']) : null;
    }

    /**
     * Get all portals.
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `client_portals` ORDER BY `added_date` DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Create a new client portal.
     */
    public static function create(array $data): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO `client_portals` (`code`, `client_name`, `email`, `event_date`, `max_selection`, `status`, `blocked`, `flagged`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $data['code'],
            $data['client_name'],
            $data['email'],
            $data['event_date'] ?? null,
            $data['max_selection'] ?? 100,
            $data['status'] ?? 'Pending',
            $data['blocked'] ?? 0,
            $data['flagged'] ?? 0
        ]);

        return $success ? new self((int)$db->lastInsertId()) : null;
    }

    /**
     * Get all photos for this client portal.
     */
    public function getPhotos(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }
        $stmt = $this->conn->prepare("SELECT * FROM `client_photos` WHERE `portal_id` = ?");
        $stmt->execute([$this->id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update the cached total photo count.
     */
    public function updatePhotoCount(): void
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }
        $stmt = $this->conn->prepare("UPDATE `client_portals` SET `total_photos` = (SELECT COUNT(*) FROM `client_photos` WHERE `portal_id` = ?) WHERE `id` = ?");
        $stmt->execute([$this->id, $this->id]);
    }
}
