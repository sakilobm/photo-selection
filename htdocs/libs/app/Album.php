<?php

namespace App;

use Aether\Traits\SQLGetterSetter;
use Aether\Database;

class Album
{
    use SQLGetterSetter;

    public string $id;
    public string $table = 'albums';
    public $conn;

    public function __construct(string $id)
    {
        $this->id = $id;
        $this->conn = Database::getConnection();
    }

    /**
     * Get all albums.
     */
    public static function getAll(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT * FROM `albums` ORDER BY `chapter` ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
