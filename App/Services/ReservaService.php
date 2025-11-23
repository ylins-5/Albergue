<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/ReservaRepository.php';
require_once __DIR__ . '/../Repositories/BedRepository.php';
require_once __DIR__ . '/../Models/Reserva.php';

use App\Repositories\ReservaRepository;
use App\Repositories\BedRepository;
use App\Models\Reserva;

class ReservaService
{
    private $reservaRepo;
    private $bedRepo;

    public function __construct()
    {
        $this->reservaRepo = new ReservaRepository();
        $this->bedRepo = new BedRepository();
    }

    public function getAll()
    {
        return $this->reservaRepo->findAll();
    }

    public function getById($id)
    {
        return $this->reservaRepo->findById($id);
    }

    public function getByUser($user_id)
    {
        return $this->reservaRepo->findByUserId($user_id);
    }

    public function getByBed($bed_id)
    {
        return $this->reservaRepo->findByBedId($bed_id);
    }

    public function create($user_id, $bed_id, $data_inicio, $data_fim)
    {
        if (!$this->reservaRepo->isBedAvailable($bed_id, $data_inicio, $data_fim)) {
            return [
                "success" => false,
                "message" => "A cama já está reservada no período escolhido."
            ];
        }

        $reserva = new Reserva(
            null,
            $user_id,
            $bed_id,
            $data_inicio,
            $data_fim
        );

        $nova = $this->reservaRepo->create($reserva);

        return [
            "success" => true,
            "data" => $nova
        ];
    }

    public function update($id, $user_id, $bed_id, $data_inicio, $data_fim)
    {
        $conflitos = $this->reservaRepo->findConflictingReservations($bed_id, $data_inicio, $data_fim);

        foreach ($conflitos as $c) {
            if ($c->id != $id) {
                return [
                    "success" => false,
                    "message" => "Conflito de reserva com o período informado."
                ];
            }
        }

        $reserva = new Reserva(
            $id,
            $user_id,
            $bed_id,
            $data_inicio,
            $data_fim
        );

        $this->reservaRepo->update($reserva);

        return [
            "success" => true,
            "data" => $reserva
        ];
    }

    public function delete($id)
    {
        return $this->reservaRepo->delete($id);
    }

    public function isBedAvailable($bed_id, $data_inicio, $data_fim)
    {
        return $this->reservaRepo->isBedAvailable($bed_id, $data_inicio, $data_fim);
    }

    public function getAvailableBeds($data_inicio, $data_fim)
    {
        return $this->reservaRepo->findAvailableBedsSQL($data_inicio, $data_fim);
    }

    public function getActiveReservations()
    {
        return $this->reservaRepo->findActive();
    }
}
