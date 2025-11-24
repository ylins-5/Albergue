<?php
namespace App\Controllers;

require_once __DIR__ . '/../Services/ReservaService.php';
require_once __DIR__ . '/../Models/Reserva.php'; // ADICIONAR

use App\Services\ReservaService;
use App\Models\Reserva; // ADICIONAR

class ReservaController {
    private $service;

    public function __construct() {
        $this->service = new ReservaService();
    }

    // ADICIONAR: Verificação de sessão centralizada
    private function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Não autorizado"]);
            return false;
        }
        return true;
    }

    // ADICIONAR: Validação de dados de entrada
    private function validateReservationData($data) {
        $errors = [];
        
        if (empty($data['bed_id'])) {
            $errors[] = "ID da cama é obrigatório";
        }
        
        if (empty($data['data_inicio'])) {
            $errors[] = "Data de início é obrigatória";
        }
        
        if (empty($data['data_fim'])) {
            $errors[] = "Data de fim é obrigatória";
        }
        
        if (!empty($data['data_inicio']) && !empty($data['data_fim'])) {
            if (strtotime($data['data_inicio']) >= strtotime($data['data_fim'])) {
                $errors[] = "Data de início deve ser anterior à data de fim";
            }
            
            // Não permitir reservas no passado
            if (strtotime($data['data_inicio']) < strtotime(date('Y-m-d'))) {
                $errors[] = "Não é possível fazer reservas para datas passadas";
            }
        }
        
        return $errors;
    }

    public function store() {
        header('Content-Type: application/json');
        
        if (!$this->checkAuth()) return;
        
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["error" => "JSON inválido"]);
            return;
        }
        
        // Validar dados
        $errors = $this->validateReservationData($data);
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados inválidos", "details" => $errors]);
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
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(["error" => "JSON inválido"]);
            return;
        }
        
        // Validar dados
        $errors = $this->validateReservationData($data);
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(["error" => "Dados inválidos", "details" => $errors]);
            return;
        }
        
        $user_id = $_SESSION['user_id']; // Usar user_id da sessão, não do input
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
            echo json_encode(["error" => "Reserva não encontrada"]);
            return;
        }
        
        echo json_encode(["success" => true]);
    }

    // ... (outros métodos) ...

    // ALTERADO: Este método agora espera um corpo JSON (POST)
    public function availableBeds() {
        header('Content-Type: application/json');
        
        // Tenta ler o JSON do corpo da requisição (padrão para POST/PUT)
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Extrai os dados do corpo JSON
        $inicio = $data['inicio'] ?? null;
        $fim = $data['fim'] ?? null;
        
        if (json_last_error() !== JSON_ERROR_NONE && empty($data)) {
            // Fallback: Se não for JSON ou for vazio, tenta ler do $_GET (se fosse GET)
            // Mas para manter a ideia do JSON, vamos falhar se não recebermos o JSON.
            http_response_code(400);
            echo json_encode(["error" => "Corpo JSON inválido ou vazio. Envie { 'inicio': 'YYYY-MM-DD', 'fim': 'YYYY-MM-DD' }"]);
            return;
        }

        if (!$inicio || !$fim) {
            http_response_code(400);
            echo json_encode(["error" => "Parâmetros 'inicio' e 'fim' são obrigatórios no JSON."]);
            return;
        }
        
        // Validar datas (mantido)
        if (!strtotime($inicio) || !strtotime($fim) || strtotime($inicio) >= strtotime($fim)) {
            http_response_code(400);
            echo json_encode(["error" => "Datas inválidas"]);
            return;
        }
        
        $beds = $this->service->getAvailableBeds($inicio, $fim);
        echo json_encode($beds);
    }
}
