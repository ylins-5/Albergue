<?php

namespace App\Repositories;
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Reserva.php';

use App\Core\Database;
use App\Models\Reserva;
use PDO;

class ReservaRepository {

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statement = $this->pdo->query("SELECT * FROM Reservas");
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $reservas = [];

        foreach ($rows as $row) {
            $reservas[] = new Reserva(
                $row['id'],
                $row['user_id'],
                $row['bed_id'],
                $row['data_inicio'],
                $row['data_fim']
            );
        }

        return $reservas;
    }

    public function findById($id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM Reservas WHERE id = ?");
        $statement->execute([$id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Reserva(
            $row['id'],
            $row['user_id'],
            $row['bed_id'],
            $row['data_inicio'],
            $row['data_fim']
        );
    }

    public function findByUserId($user_id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM Reservas WHERE user_id = ?");
        $statement->execute([$user_id]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $reservas = [];

        foreach ($rows as $row) {
            $reservas[] = new Reserva(
                $row['id'],
                $row['user_id'],
                $row['bed_id'],
                $row['data_inicio'],
                $row['data_fim']
            );
        }

        return $reservas;
    }

    public function findByBedId($bed_id)
    {
        $statement = $this->pdo->prepare("SELECT * FROM Reservas WHERE bed_id = ?");
        $statement->execute([$bed_id]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $reservas = [];

        foreach ($rows as $row) {
            $reservas[] = new Reserva(
                $row['id'],
                $row['user_id'],
                $row['bed_id'],
                $row['data_inicio'],
                $row['data_fim']
            );
        }

        return $reservas;
    }

    public function create(Reserva $reserva)
    {
        $statement = $this->pdo->prepare("
            INSERT INTO Reservas (user_id, bed_id, data_inicio, data_fim)
            VALUES (?, ?, ?, ?)
        ");

        $statement->execute([
            $reserva->user_id,
            $reserva->bed_id,
            $reserva->data_inicio,
            $reserva->data_fim
        ]);

        return $this->findById($this->pdo->lastInsertId());
    }

    public function update(Reserva $reserva)
    {
        $statement = $this->pdo->prepare("
            UPDATE Reservas SET user_id = ?, bed_id = ?, data_inicio = ?, data_fim = ?
            WHERE id = ?
        ");

        return $statement->execute([
            $reserva->user_id,
            $reserva->bed_id,
            $reserva->data_inicio,
            $reserva->data_fim,
            $reserva->id
        ]);
    }

    public function delete($id)
    {
        $statement = $this->pdo->prepare("DELETE FROM Reservas WHERE id = ?");
        return $statement->execute([$id]);
    }

    public function isBedAvailable($bed_id, $data_inicio, $data_fim)
    {
        $statement = $this->pdo->prepare("
            SELECT COUNT(*) as total
            FROM Reservas
            WHERE bed_id = ?
              AND (
                    (data_inicio <= ? AND data_fim >= ?) OR 
                    (data_inicio <= ? AND data_fim >= ?) OR
                    (data_inicio >= ? AND data_fim <= ?)
                  )
        ");

        $statement->execute([
            $bed_id,
            $data_inicio, $data_inicio,
            $data_fim, $data_fim,
            $data_inicio, $data_fim
        ]);

        $result = $statement->fetch(PDO::FETCH_ASSOC);

        return $result['total'] == 0;
    }

    public function findConflictingReservations($bed_id, $data_inicio, $data_fim)
    {
        $statement = $this->pdo->prepare("
            SELECT * FROM Reservas
            WHERE bed_id = ?
              AND (
                    (data_inicio <= ? AND data_fim >= ?) OR 
                    (data_inicio <= ? AND data_fim >= ?) OR
                    (data_inicio >= ? AND data_fim <= ?)
                  )
        ");

        $statement->execute([
            $bed_id,
            $data_inicio, $data_inicio,
            $data_fim, $data_fim,
            $data_inicio, $data_fim
        ]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $reservas = [];

        foreach ($rows as $row) {
            $reservas[] = new Reserva(
                $row['id'],
                $row['user_id'],
                $row['bed_id'],
                $row['data_inicio'],
                $row['data_fim']
            );
        }

        return $reservas;
    }

    public function findActive()
    {
        $now = date("Y-m-d");

        $statement = $this->pdo->prepare("
            SELECT * FROM Reservas 
            WHERE data_inicio <= ? AND data_fim >= ?
        ");

        $statement->execute([$now, $now]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $reservas = [];

        foreach ($rows as $row) {
            $reservas[] = new Reserva(
                $row['id'],
                $row['user_id'],
                $row['bed_id'],
                $row['data_inicio'],
                $row['data_fim']
            );
        }

        return $reservas;
    }

    public function findAvailableBeds($data_inicio, $data_fim)
    {
    $statement = $this->pdo->prepare("
        SELECT * FROM Beds
        WHERE id NOT IN (
            SELECT bed_id FROM Reservas
            WHERE
                (data_inicio <= ? AND data_fim >= ?) OR
                (data_inicio <= ? AND data_fim >= ?) OR
                (data_inicio >= ? AND data_fim <= ?)
        )
    ");

    $statement->execute([
        $data_inicio, $data_inicio,
        $data_fim, $data_fim,
        $data_inicio, $data_fim
    ]);

    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    $beds = [];
    foreach ($rows as $row) {
        $beds[] = new \App\Models\Bed(
            $row['id'],
            $row['quarto_id'],
            $row['numero']
        );
    }

    return $beds;
    }
}
