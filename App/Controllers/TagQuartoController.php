<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/TagQuartoService.php';

use App\Services\TagQuartoService;

class TagQuartoController {

    private $service;

    public function __construct()
    {
        $this->service = new TagQuartoService();
    }

    public function getAll()
    {
        $result = $this->service->getAll();
        echo json_encode($result);
    }

    public function getById($tag_id, $quarto_id)
    {
        $result = $this->service->getByIds($tag_id, $quarto_id);
        echo json_encode($result);
    }

    public function getByTag($tag_id)
    {
        $result = $this->service->getByTag($tag_id);
        echo json_encode($result);
    }

    public function getByQuarto($quarto_id)
    {
        $result = $this->service->getByQuarto($quarto_id);
        echo json_encode($result);
    }
    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $created = $this->service->create($data);
        echo json_encode(['success' => $created]);
    }

    public function delete($tag_id, $quarto_id)
    {
        $deleted = $this->service->delete($tag_id, $quarto_id);
        echo json_encode(['success' => $deleted]);
    }
}
