<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/TagCamaRepository.php';
require_once __DIR__ . '/../Models/TagCama.php';

use App\Repositories\TagCamaRepository;
use App\Models\TagCama;

class TagCamaService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new TagCamaRepository();
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getByIds($tag_id, $cama_id)
    {
        return $this->repository->findByIds($tag_id, $cama_id);
    }

    public function getByTag($tag_id)
    {
        return $this->repository->findByTag($tag_id);
    }

    public function getByCama($cama_id)
    {
        return $this->repository->findByCama($cama_id);
    }

    public function create($data)
    {
        $tagCama = new TagCama(
            $data['tag_id'] ?? null,
            $data['cama_id'] ?? null
        );

        return $this->repository->create($tagCama);
    }

    public function delete($tag_id, $cama_id)
    {
        return $this->repository->delete($tag_id, $cama_id);
    }
}
