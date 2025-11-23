<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/BedService.php';

use App\Services\BedService;

class BedController
{
    private $service;

    public function __construct()
    {
        $this->service = new BedService();
    }

    public function getAll()
    {
        $beds = $this->service->getAllBeds();
        echo json_encode($beds);
    }

    public function getById($id)
    {
        $bed = $this->service->getBedById($id);

        if (!$bed) {
            http_response_code(404);
            echo json_encode(["error" => "Cama não encontrada"]);
            return;
        }

        echo json_encode($bed);
    }

    public function getByRoomId($quarto_id)
    {
        $beds = $this->service->getBedsByRoomId($quarto_id);
        echo json_encode($beds);
    }

    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['quarto_id']) || !isset($data['numero'])) {
            http_response_code(400);
            echo json_encode(["error" => "Dados inválidos"]);
            return;
        }

        $bed = $this->service->createBed($data);
        echo json_encode($bed);
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $bed = $this->service->updateBed($id, $data);

        if (!$bed) {
            http_response_code(404);
            echo json_encode(["error" => "Cama não encontrada"]);
            return;
        }

        echo json_encode($bed);
    }

    public function delete($id)
    {
        $success = $this->service->deleteBed($id);

        if (!$success) {
            http_response_code(404);
            echo json_encode(["error" => "Cama não encontrada"]);
            return;
        }

        echo json_encode(["message" => "Cama deletada com sucesso"]);
    }
}
