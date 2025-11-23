<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/ReservaService.php';

use App\Services\ReservaService;

class ReservaController
{
    private $service;

    public function __construct()
    {
        $this->service = new ReservaService();
    }

    public function index()
    {
        header('Content-Type: application/json');
        $reservas = $this->service->getAll();
        echo json_encode($reservas);
    }

    public function show($id)
    {
        header('Content-Type: application/json');
        $reserva = $this->service->getById($id);

        if (!$reserva) {
            http_response_code(404);
            echo json_encode(["error" => "Reserva não encontrada"]);
            return;
        }

        echo json_encode($reserva);
    }

    public function store()
    {
        header('Content-Type: application/json');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Você precisa estar logado."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $_SESSION['user_id'];

        $bed_id = $data['bed_id'] ?? null;
        $data_inicio = $data['data_inicio'] ?? $data['data_entrada'] ?? null;
        $data_fim = $data['data_fim'] ?? $data['data_saida'] ?? null;
        
        if(!$bed_id || !$data_inicio || !$data_fim) {
            http_response_code(400);
            echo json_encode(["error" => "Dados incompletos (Cama ou Datas)."]);
            return;
        }

        $result = $this->service->create($user_id, $bed_id, $data_inicio, $data_fim);

        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }

        http_response_code(201);
        echo json_encode($result["data"]);
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Não autorizado"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $user_id = $data['user_id'] ?? $_SESSION['user_id']; 
        $bed_id = $data['bed_id'] ?? null;
        $data_inicio = $data['data_inicio'] ?? null;
        $data_fim = $data['data_fim'] ?? null;

        $result = $this->service->update($id, $user_id, $bed_id, $data_inicio, $data_fim);

        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }

        echo json_encode($result["data"]);
    }

    public function destroy($id)
    {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return;
        }

        $this->service->delete($id);
        echo json_encode(["success" => true]);
    }

    public function availableBeds()
    {
        header('Content-Type: application/json');
        $inicio = $_GET['inicio'] ?? null;
        $fim = $_GET['fim'] ?? null;

        if (!$inicio || !$fim) {
            http_response_code(400);
            echo json_encode(["error" => "Envie inicio e fim"]);
            return;
        }

        $beds = $this->service->getAvailableBeds($inicio, $fim);

        echo json_encode($beds);
    }
}