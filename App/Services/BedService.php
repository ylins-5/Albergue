<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/BedRepository.php';

use App\Repositories\BedRepository;
use App\Models\Bed;

class BedService
{
    private $repository;

    public function __construct()
    {
        $this->repository = new BedRepository();
    }

    public function getAllBeds()
    {
        return $this->repository->findAll();
    }

    public function getBedById($id)
    {
        return $this->repository->findById($id);
    }

    public function getBedsByRoomId($quarto_id)
    {
        return $this->repository->findByRoomId($quarto_id);
    }

    public function createBed($data)
    {
        $bed = new Bed(
            null,
            $data['quarto_id'] ?? null,
            $data['numero'] ?? null
        );

        return $this->repository->create($bed);
    }

    public function updateBed($id, $data)
    {
        $existing = $this->repository->findById($id);

        if (!$existing) {
            return null; 
        }

        $existing->quarto_id = $data['quarto_id'] ?? $existing->quarto_id;
        $existing->numero = $data['numero'] ?? $existing->numero;

        $this->repository->update($existing);

        return $this->repository->findById($id);
    }

    public function deleteBed($id)
    {
        return $this->repository->delete($id);
    }
}
