<?php

namespace App\Repositories;

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/TagCama.php';

use App\Core\Database;
use App\Models\TagCama;
use PDO;

class TagCamaRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statement = $this->pdo->query("SELECT * FROM tags_camas");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByIds($tag_id, $cama_id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM tags_camas WHERE tag_id = ? AND cama_id = ?"
        );
        $statement->execute([$tag_id, $cama_id]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function findByTag($tag_id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM tags_camas WHERE tag_id = ?"
        );
        $statement->execute([$tag_id]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByCama($cama_id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM tags_camas WHERE cama_id = ?"
        );
        $statement->execute([$cama_id]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(TagCama $tagCama)
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO tags_camas (tag_id, cama_id) VALUES (?, ?)"
        );

        return $statement->execute([
            $tagCama->tag_id,
            $tagCama->cama_id
        ]);
    }

    public function delete($tag_id, $cama_id)
    {
        $statement = $this->pdo->prepare(
            "DELETE FROM tags_camas WHERE tag_id = ? AND cama_id = ?"
        );

        return $statement->execute([$tag_id, $cama_id]);
    }
}
