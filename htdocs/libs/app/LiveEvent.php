<?php

namespace App;

use Aether\Traits\SQLGetterSetter;
use Aether\Database;

class LiveEvent
{
    use SQLGetterSetter;

    public int $id;
    public string $table = 'live_event';
    public $conn;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->conn = Database::getConnection();
    }

    /**
     * Get the active live event details.
     */
    public static function getActive(): ?self
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT `id` FROM `live_event` LIMIT 1");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $row ? new self((int)$row['id']) : null;
    }

    /**
     * Get event details as array.
     */
    public function getDetails(): array
    {
        if (!$this->conn) {
            $this->conn = Database::getConnection();
        }
        $stmt = $this->conn->prepare("SELECT * FROM `live_event` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$this->id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}
