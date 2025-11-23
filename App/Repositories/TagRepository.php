<?php

namespace App\Repositories;
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Tag.php';

use App\Core\Database;
use App\Models\Tag;
use PDO;

class TagRepository {

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statement = $this->pdo->query("SELECT * FROM Tags");
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $tags = [];

        foreach ($rows as $row) {
            $tags[] = new Tag(
                $row['id'],
                $row['nome'],
                $row['tipo']
            );
        }

        return $tags;
    }

    public function findById($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM Tags WHERE id = ?");
        $statement->execute([$id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Tag(
            $row['id'],
            $row['nome'],
            $row['tipo']
        );
    }

    public function create(Tag $tag)
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO Tags (nome, tipo) VALUES (?, ?)"
        );

        $statement->execute([$tag->nome, $tag->tipo]);

        return $this->findById($this->pdo->lastInsertId());
    }

    public function update(Tag $tag)
    {
        $statement = $this->pdo->prepare(
            "UPDATE Tags SET nome = ?, tipo = ? WHERE id = ?"
        );

        return $statement->execute([$tag->nome, $tag->tipo, $tag->id]);
    }

    public function delete($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM Tags WHERE id = ?");
        return $statement->execute([$id]);
    }

    public function findByType($tipo)
    {
    $statement = $this->pdo->prepare("SELECT * FROM tags WHERE tipo = :tipo");
    $statement->bindValue(':tipo', $tipo);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
