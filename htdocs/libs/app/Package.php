<?php

namespace App;

use Aether\Traits\SQLGetterSetter;
use Aether\Database;

class Package
{
    use SQLGetterSetter;

    public string $id;
    public string $table = 'packages';
    public $conn;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->conn = Database::getConnection();
    }

    /**
     * Get all packages sorted.
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `packages` ORDER BY `price` ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
