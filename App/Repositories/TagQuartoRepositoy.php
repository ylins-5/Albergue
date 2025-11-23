<?php

namespace App\Repositories;

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/TagQuarto.php';

use App\Core\Database;
use App\Models\TagQuarto;
use PDO;

class TagQuartoRepository {

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statement = $this->pdo->query("SELECT * FROM tags_quartos");
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $result = [];

        foreach ($rows as $row) {
            $item = new TagQuarto();
            $item->tag_id = $row['tag_id'];
            $item->quarto_id = $row['quarto_id'];
            $result[] = $item;
        }

        return $result;
    }

    public function findByIds($tag_id, $quarto_id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM tags_quartos WHERE tag_id = ? AND quarto_id = ?"
        );

        $statement->execute([$tag_id, $quarto_id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        $item = new TagQuarto();
        $item->tag_id = $row['tag_id'];
        $item->quarto_id = $row['quarto_id'];

        return $item;
    }

    public function findByTag($tag_id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM tags_quartos WHERE tag_id = ?"
        );

        $statement->execute([$tag_id]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByQuarto($quarto_id)
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM tags_quartos WHERE quarto_id = ?"
        );

        $statement->execute([$quarto_id]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(TagQuarto $tq)
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO tags_quartos (tag_id, quarto_id) VALUES (?, ?)"
        );

        return $statement->execute([$tq->tag_id, $tq->quarto_id]);
    }

    public function delete($tag_id, $quarto_id)
    {
        $statement = $this->pdo->prepare(
            "DELETE FROM tags_quartos WHERE tag_id = ? AND quarto_id = ?"
        );

        return $statement->execute([$tag_id, $quarto_id]);
    }
}
