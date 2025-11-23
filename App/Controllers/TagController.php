<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/TagService.php';

use App\Services\TagService;

class TagController
{
    private $service;

    public function __construct()
    {
        $this->service = new TagService();
    }

    public function index()
    {
        $tags = $this->service->getAllTags();
        echo json_encode($tags);
    }

    public function show($id)
    {
        $tag = $this->service->getTagsById($id);

        if (!$tag) {
            http_response_code(404);
            echo json_encode(['error' => 'Tag não encontrada']);
            return;
        }

        echo json_encode($tag);
    }

    public function store()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $tag = $this->service->createTag($data);

        http_response_code(201);
        echo json_encode($tag);
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $tag = $this->service->updateTag($id, $data);
        if (!$tag) {
            http_response_code(404);
            echo json_encode(['error' => 'Tag não encontrada']);
            return;
        }

        echo json_encode($tag);
    }

    public function delete($id)
    {
        $deleted = $this->service->deleteTag($id);

        if (!$deleted) {
            http_response_code(404);
            echo json_encode(['error' => 'Tag não encontrada']);
            return;
        }

        echo json_encode(['success' => true]);
    }

    public function findByType($tipo)
    {
    $tags = $this->service->getTagsByType($tipo);
    if (!$tags || count($tags) === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Nenhuma tag encontrada com esse tipo']);
        return;
    }

    echo json_encode($tags);
    }
}
