<?php
namespace App\Repositories;

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/Reserva.php';
require_once __DIR__ . '/../Models/Bed.php'; // ADICIONAR

use App\Core\Database;
use App\Models\Reserva;
use App\Models\Bed; // ADICIONAR
use PDO;
use PDOException; // ADICIONAR

class ReservaRepository {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll() {
        try {
            $statement = $this->pdo->query("SELECT * FROM reservas"); // tabela em minúsculo
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
        } catch (PDOException $e) {
            throw new \Exception("Erro ao buscar reservas: " . $e->getMessage());
        }
    }

    public function findById($id) {
        try {
            $statement = $this->pdo->prepare("SELECT * FROM reservas WHERE id = ?");
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
        } catch (PDOException $e) {
            throw new \Exception("Erro ao buscar reserva: " . $e->getMessage());
        }
    }

public function isBedAvailable($data_inicio, $data_fim, $exclude_reserva_id = null) {
    try {
        // Buscar todas as camas
        $sql = "SELECT id, numero FROM beds";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $allBeds = $statement->fetchAll(PDO::FETCH_ASSOC);

        $availableBeds = [];

        foreach ($allBeds as $bed) {
            $sqlReserva = "SELECT COUNT(*) as total FROM reservas 
                           WHERE bed_id = ? 
                           AND data_inicio < ? 
                           AND data_fim > ?";
            

            $params = [$bed['id'], $data_fim, $data_inicio];

            if ($exclude_reserva_id) {
                $sqlReserva .= " AND id != ?";
                $params[] = $exclude_reserva_id;
            }

            $stmt = $this->pdo->prepare($sqlReserva);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] == 0) {
                $availableBeds[] = $bed; // Cama disponível
            }
        }

        return $availableBeds;

    } catch (\PDOException $e) {
        throw new \Exception("Erro ao buscar camas disponíveis: " . $e->getMessage());
    }
}


    public function hasUserReservationInPeriod($user_id, $data_inicio, $data_fim, $exclude_reserva_id = null) {
    try {
        $sql = "SELECT COUNT(*) as total FROM reservas 
                WHERE user_id = ? 
                AND data_inicio < ? 
                AND data_fim > ?"; 

        $params = [$user_id, $data_fim, $data_inicio];

        if ($exclude_reserva_id) {
            $sql .= " AND id != ?";
            $params[] = $exclude_reserva_id;
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] > 0;
    } catch (PDOException $e) {
        throw new \Exception("Erro ao verificar reservas do usuário: " . $e->getMessage());
    }
    }


    public function create(Reserva $reserva) {
            try {
                // Validar datas
                $inicio = strtotime($reserva->data_inicio);
                $fim = strtotime($reserva->data_fim);
                $now = time();
                
                if ($inicio === false || $fim === false) {
                    throw new \Exception("Datas fornecidas são inválidas");
                }
                
                if ($inicio >= $fim) {
                    throw new \Exception("Data de início deve ser anterior à data de fim");
                }
                
                if ($inicio < $now) {
                    throw new \Exception("Data de início não pode ser no passado");
                }
            
            $statement = $this->pdo->prepare("
                INSERT INTO reservas (user_id, bed_id, data_inicio, data_fim) 
                VALUES (?, ?, ?, ?)
            ");
            
            $success = $statement->execute([
                $reserva->user_id,
                $reserva->bed_id,
                $reserva->data_inicio,
                $reserva->data_fim
            ]);
            
            if (!$success) {
                throw new \Exception("Erro ao criar reserva");
            }
            
            return $this->findById($this->pdo->lastInsertId());
        } catch (PDOException $e) {
            throw new \Exception("Erro ao criar reserva: " . $e->getMessage());
        }
    }

    // ADICIONAR: Transação para operações críticas
    public function createWithValidation(Reserva $reserva) {
        try {
            $this->pdo->beginTransaction();
            
            // Verificar disponibilidade
            if (!$this->isBedAvailable($reserva->bed_id, $reserva->data_inicio, $reserva->data_fim)) {
                throw new \Exception("Cama não disponível no período solicitado");
            }
            
            // Verificar se usuário já tem reserva no período
            if ($this->hasUserReservationInPeriod($reserva->user_id, $reserva->data_inicio, $reserva->data_fim)) {
                throw new \Exception("Usuário já possui reserva no período solicitado");
            }
            
            $result = $this->create($reserva);
            $this->pdo->commit();
            
            return $result;
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Buscar reservas de um usuário específico
public function findByUserId($user_id) {
    try {
        $statement = $this->pdo->prepare("SELECT * FROM reservas WHERE user_id = ?");
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
    } catch (PDOException $e) {
        throw new \Exception("Erro ao buscar reservas do usuário: " . $e->getMessage());
    }
}

}