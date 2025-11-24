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
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(["error" => "Sessão expirada ou usuário não logado. Faça login novamente."]);
            return false;
        }
        return true;
    }

    public function index() {
        header('Content-Type: application/json');
        try {
            $reservas = $this->service->getAllReservations();
            echo json_encode($reservas);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function userReservations() {
        header('Content-Type: application/json');
        if (!$this->checkAuth()) return;
        
        $user_id = $_SESSION['user_id'];
        $repo = new \App\Repositories\ReservaRepository();
        $reservas = $repo->findByUserIdWithDetails($user_id);
        echo json_encode($reservas);
    }

    public function store() {
        header('Content-Type: application/json');
        
        // Verifica sessão antes de qualquer coisa
        if (!$this->checkAuth()) return;
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['bed_id']) || empty($data['data_inicio']) || empty($data['data_fim'])) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos."]);
            return;
        }
        
        // Pega o ID da sessão (Garante que não seja null)
        $user_id = $_SESSION['user_id'];
        
        $result = $this->service->create($user_id, $data['bed_id'], $data['data_inicio'], $data['data_fim']);
        
        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }
        
        http_response_code(201);
        echo json_encode($result["data"]);
    }

    public function destroy($id) {
        header('Content-Type: application/json');
        if (!$this->checkAuth()) return;
        
        $success = $this->service->delete($id);
        
        if (!$success) {
            http_response_code(404);
            echo json_encode(["error" => "Erro ao cancelar."]);
            return;
        }
        echo json_encode(["success" => true]);
    }
    
    public function update($id) {
        header('Content-Type: application/json');
        if (!$this->checkAuth()) return;
        
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION['user_id'];
        
        // Busca reserva atual para manter o bed_id se não for enviado
        $repo = new \App\Repositories\ReservaRepository();
        $atual = $repo->findById($id);
        
        if (!$atual) {
            http_response_code(404);
            echo json_encode(["error" => "Reserva não encontrada"]);
            return;
        }

        $bed_id = $atual->bed_id; 

        $result = $this->service->update($id, $user_id, $bed_id, $data['data_inicio'], $data['data_fim']);
        
        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }
        echo json_encode($result["data"]);
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