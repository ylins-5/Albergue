<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/TagCamaService.php';

use App\Services\TagCamaService;

class TagCamaController
{
    private $service;

    public function __construct()
    {
        $this->service = new TagCamaService();
    }

    public function getAll()
    {
        echo json_encode($this->service->getAll());
    }

    public function getById($tag_id, $cama_id)
    {
        echo json_encode($this->service->getByIds($tag_id, $cama_id));
    }

    public function getByTag($tag_id)
    {
        echo json_encode($this->service->getByTag($tag_id));
    }

    public function getByCama($cama_id)
    {
        echo json_encode($this->service->getByCama($cama_id));
    }

    public function create()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        echo json_encode(['success' => $this->service->create($data)]);
    }

    public function delete($tag_id, $cama_id)
    {
        echo json_encode(['success' => $this->service->delete($tag_id, $cama_id)]);
    }
}
