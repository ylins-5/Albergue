<?php
namespace App\Services;

require_once __DIR__ . '/../Repositories/ReservaRepository.php';
require_once __DIR__ . '/../Repositories/BedRepository.php';
require_once __DIR__ . '/../Models/Reserva.php';

use App\Repositories\ReservaRepository;
use App\Repositories\BedRepository;
use App\Models\Reserva;

class ReservaService {
    private $reservaRepo;
    private $bedRepo;

    public function __construct(ReservaRepository $reservaRepo = null, BedRepository $bedRepo = null) {
        $this->reservaRepo = $reservaRepo ?? new ReservaRepository();
        $this->bedRepo = $bedRepo ?? new BedRepository();
    }

    public function create($user_id, $bed_id, $data_inicio, $data_fim) {
        try {
            // Verificar existência da cama
            $bed = $this->bedRepo->findById($bed_id);
            if (!$bed) {
                return ["success" => false, "message" => "Cama não encontrada."];
            }

            // Verificar disponibilidade (regra de negócio)
            if (!$this->reservaRepo->isBedAvailable($bed_id, $data_inicio, $data_fim)) {
                 return ["success" => false, "message" => "Esta cama já está reservada para este período."];
            }

            $reserva = new Reserva(null, $user_id, $bed_id, $data_inicio, $data_fim);
            
            // Assume que o Repository tem método create
            $nova = $this->reservaRepo->create($reserva);
            
            return ["success" => true, "data" => $nova];
            
        } catch (\Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $user_id, $bed_id, $data_inicio, $data_fim) {
        try {
            $reservaExistente = $this->reservaRepo->findById($id);
            if (!$reservaExistente) {
                return ["success" => false, "message" => "Reserva não encontrada."];
            }

            // Verifica conflito ignorando a própria reserva atual
            if (!$this->reservaRepo->isBedAvailable($bed_id, $data_inicio, $data_fim, $id)) {
                return ["success" => false, "message" => "Conflito de datas."];
            }

            // 7. Verificar conflitos de Usuário (ADICIONADO - excluindo a própria reserva)
            if ($this->reservaRepo->hasUserReservationInPeriod($user_id, $data_inicio, $data_fim, $id)) {
                return ["success" => false, "message" => "Conflito de reserva: Usuário já possui reserva no período solicitado."];
            }

            // 8. Criar objeto e atualizar
            $reserva = new Reserva($id, $user_id, $bed_id, $data_inicio, $data_fim);
            
            // ATENÇÃO: É necessário implementar o método update no ReservaRepository
            $success = $this->reservaRepo->update($reserva); 
            
            if (!$success) {
                return ["success" => false, "message" => "Erro ao atualizar."];
            }
            
            return ["success" => true, "data" => $reserva];
            
        } catch (\Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->reservaRepo->delete($id);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAvailableBeds($data_inicio, $data_fim) {
        return $this->reservaRepo->findAvailableBeds($data_inicio, $data_fim);
    }
}