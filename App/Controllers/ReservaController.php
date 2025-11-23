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
        $reservas = $this->service->getAll();
        echo json_encode($reservas);
    }

    public function show($id)
    {
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
        $data = json_decode(file_get_contents("php://input"), true);

        $user_id = $data['user_id'] ?? null;
        $bed_id = $data['bed_id'] ?? null;
        $data_inicio = $data['data_inicio'] ?? null;
        $data_fim = $data['data_fim'] ?? null;

        $result = $this->service->create($user_id, $bed_id, $data_inicio, $data_fim);

        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }

        echo json_encode($result["data"]);
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $user_id = $data['user_id'] ?? null;
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
        $this->service->delete($id);
        echo json_encode(["success" => true]);
    }

    public function availableBeds()
    {
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
