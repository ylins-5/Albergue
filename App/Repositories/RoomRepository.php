<?php

namespace App\Repositories;
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Room.php';


use App\Core\Database;
use App\Models\Room;
use PDO;

class RoomRepository{

    private $PDO;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statment = $this->pdo->query("SELECT * FROM Rooms");
        $rows = $statment->fetchAll(PDO::FETCH_ASSOC);

        $Rooms = [];

        foreach ($rows as $row) {
            $Rooms[] = new Room(
                $row['id'],
                $row['numero'],
                $row['descricao'],
                $row['imagem']
            );
        }

        return $Rooms;
    }

    public function findById($id)
    {
        $statment = $this->pdo->prepare("SELECT * FROM Rooms WHERE id = ? ");
        $statment->execute([$id]);

        $row = $statment->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Room(
            $row['id'],
            $row['numero'],
            $row['descricao'],
            $row['imagem']);
    }

    public function Create(Room $Room){

        $statment = $this->pdo->prepare("INSERT INTO Rooms (numero, descricao, imagem) VALUES (?,?,?)");
        $statment->execute([$Room->numero, $Room->descricao, $Room->imagem]);

        return $this->findById($this->pdo->lastInsertId());
    }

    public function update(Room $Room)
    {
          $statment = $this->pdo->prepare("UPDATE Rooms SET numero = ?, descricao = ?, imagem = ? WHERE id = ?");
          return $statment->execute([$Room->numero, $Room->descricao, $Room->imagem, $Room->id]);
    }

    public function delete($id)
    {
        $statment = $this->pdo->prepare("DELETE FROM Rooms WHERE id = ?");
        return $statment->execute([$id]);
    }
}