<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/TagQuartoRepository.php';
require_once __DIR__ . '/../Models/TagQuarto.php';

use App\Repositories\TagQuartoRepository;
use App\Models\TagQuarto;

class TagQuartoService {

    private $repository;

    public function __construct()
    {
        $this->repository = new TagQuartoRepository();
    }

    public function getAll()
    {
        return $this->repository->findAll();
    }

    public function getByIds($tag_id, $quarto_id)
    {
        return $this->repository->findByIds($tag_id, $quarto_id);
    }

    public function getByTag($tag_id)
    {
        return $this->repository->findByTag($tag_id);
    }

    public function getByQuarto($quarto_id)
    {
        return $this->repository->findByQuarto($quarto_id);
    }

    public function create($data)
    {
        $tq = new TagQuarto();
        $tq->tag_id = $data['tag_id'] ?? null;
        $tq->quarto_id = $data['quarto_id'] ?? null;

        return $this->repository->create($tq);
    }

    public function delete($tag_id, $quarto_id)
    {
        return $this->repository->delete($tag_id, $quarto_id);
    }
}
