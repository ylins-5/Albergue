<?php
namespace App\Controllers;

require_once __DIR__ . '/../Services/ReservaService.php';
require_once __DIR__ . '/../Models/Reserva.php';

use App\Services\ReservaService;
use App\Models\Reserva;

class ReservaController {
    private $service;

    public function __construct() {
        $this->service = new ReservaService();
    }

    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Usuário não autenticado. Faça login."]);
            return false;
        }
        return true;
    }

    private function validateReservationData($data) {
        $errors = [];
        
        if (empty($data['bed_id'])) $errors[] = "Selecione uma cama.";
        if (empty($data['data_inicio'])) $errors[] = "Data de check-in obrigatória.";
        if (empty($data['data_fim'])) $errors[] = "Data de check-out obrigatória.";
        
        if (!empty($data['data_inicio']) && !empty($data['data_fim'])) {
            $inicio = strtotime($data['data_inicio']);
            $fim = strtotime($data['data_fim']);
            $hoje = strtotime(date('Y-m-d'));

            if ($inicio >= $fim) {
                $errors[] = "A data de check-out deve ser após o check-in.";
            }
            if ($inicio < $hoje) {
                $errors[] = "Não é possível reservar para datas passadas.";
            }
        }
        return $errors;
    }

    public function store() {
        header('Content-Type: application/json');
        
        if (!$this->checkAuth()) return;
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $errors = $this->validateReservationData($data);
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => implode(" ", $errors)]);
            return;
        }
        
        $user_id = $_SESSION['user_id'];
        $bed_id = $data['bed_id'];
        $data_inicio = $data['data_inicio'];
        $data_fim = $data['data_fim'];
        
        $result = $this->service->create($user_id, $bed_id, $data_inicio, $data_fim);
        
        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }
        
        http_response_code(201);
        echo json_encode($result["data"]);
    }

    public function update($id) {
        header('Content-Type: application/json');
        
        if (!$this->checkAuth()) return;
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        $errors = $this->validateReservationData($data);
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => implode(" ", $errors)]);
            return;
        }
        
        $user_id = $_SESSION['user_id'];
        $bed_id = $data['bed_id'];
        $data_inicio = $data['data_inicio'];
        $data_fim = $data['data_fim'];
        
        $result = $this->service->update($id, $user_id, $bed_id, $data_inicio, $data_fim);
        
        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }
        
        echo json_encode($result["data"]);
    }

    public function destroy($id) {
        header('Content-Type: application/json');
        
        if (!$this->checkAuth()) return;
        
        $success = $this->service->delete($id);
        
        if (!$success) {
            http_response_code(404);
            echo json_encode(["error" => "Erro ao cancelar reserva."]);
            return;
        }
        
        echo json_encode(["success" => true, "message" => "Reserva cancelada."]);
    }

    public function availableBeds() {
        header('Content-Type: application/json');
        
        $inicio = $_GET['inicio'] ?? null;
        $fim = $_GET['fim'] ?? null;
        
        if (!$inicio || !$fim) {
            http_response_code(400);
            echo json_encode(["error" => "Datas são obrigatórias"]);
            return;
        }
        
        $beds = $this->service->getAvailableBeds($inicio, $fim);
        echo json_encode($beds);
    }
}