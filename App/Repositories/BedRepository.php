<?php

namespace App\Repositories;
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Bed.php';

use App\Core\Database;
use App\Models\Bed;
use \App\Models\Room;
use PDO;

class BedRepository {

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statment = $this->pdo->query("SELECT * FROM Beds");
        $rows = $statment->fetchAll(PDO::FETCH_ASSOC);

        $beds = [];

        foreach ($rows as $row) {
            $beds[] = new Bed(
                $row['id'],
                $row['quarto_id'],
                $row['numero'],
            );
        }

        return $beds;
    }

    public function findById($id)
    {
        $statment = $this->pdo->prepare("SELECT * FROM Beds WHERE id = ?");
        $statment->execute([$id]);

        $row = $statment->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Bed(
            $row['id'],
            $row['quarto_id'],
            $row['numero'],
        );
    }

    public function findByRoomId($quarto_id)
    {
        $statment = $this->pdo->prepare("SELECT * FROM Beds WHERE quarto_id = ?");
        $statment->execute([$quarto_id]);
        
        $rows = $statment->fetchAll(PDO::FETCH_ASSOC);

        $beds = [];
        foreach ($rows as $row) {
            $beds[] = new Bed(
                $row['id'],
                $row['quarto_id'],
                $row['numero'],
            );
        }

        return $beds;
    }

    public function create(Bed $bed)
    {
        $statment = $this->pdo->prepare("
            INSERT INTO Beds (quarto_id, numero)
            VALUES (?, ?)
        ");

        $statment->execute([
            $bed->quarto_id,
            $bed->numero,
        ]);

        return $this->findById($this->pdo->lastInsertId());
    }

    public function update(Bed $bed)
    {
        $statment = $this->pdo->prepare("
            UPDATE Beds SET quarto_id = ?, numero = ? WHERE id = ?");

        return $statment->execute([
            $bed->quarto_id,
            $bed->numero,
            $bed->id
        ]);
    }

    public function delete($id)
    {
        $statment = $this->pdo->prepare("DELETE FROM Beds WHERE id = ?");
        return $statment->execute([$id]);
    }

}
