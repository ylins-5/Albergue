<?php
namespace App\Services;

require_once __DIR__ . '/../Repositories/ReservaRepository.php';
require_once __DIR__ . '/../Repositories/BedRepository.php';
require_once __DIR__ . '/../Models/Reserva.php'; // ADICIONAR

use App\Repositories\ReservaRepository;
use App\Repositories\BedRepository;
use App\Models\Reserva; // ADICIONAR

class ReservaService {
    private $reservaRepo;
    private $bedRepo;

    public function __construct(ReservaRepository $reservaRepo = null, BedRepository $bedRepo = null) {
        $this->reservaRepo = $reservaRepo ?? new ReservaRepository();
        $this->bedRepo = $bedRepo ?? new BedRepository();
    }

    public function create($user_id, $bed_id, $data_inicio, $data_fim) {
        try {
            // Validar dados básicos
            if (empty($user_id) || empty($bed_id) || empty($data_inicio) || empty($data_fim)) {
                return [
                    "success" => false,
                    "message" => "Todos os campos são obrigatórios."
                ];
            }

            // Validar formato das datas
            if (!strtotime($data_inicio) || !strtotime($data_fim)) {
                return [
                    "success" => false,
                    "message" => "Datas inválidas."
                ];
            }

            // Verificar se a cama existe
            $bed = $this->bedRepo->findById($bed_id);
            if (!$bed) {
                return [
                    "success" => false,
                    "message" => "Cama não encontrada."
                ];
            }

            $reserva = new Reserva(null, $user_id, $bed_id, $data_inicio, $data_fim);
            $nova = $this->reservaRepo->createWithValidation($reserva);
            
            return [
                "success" => true,
                "data" => $nova
            ];
            
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function update($id, $user_id, $bed_id, $data_inicio, $data_fim) {
        try {
            // Buscar reserva existente
            $reservaExistente = $this->reservaRepo->findById($id);
            if (!$reservaExistente) {
                return [
                    "success" => false,
                    "message" => "Reserva não encontrada."
                ];
            }

            // Validar dados
            if (empty($user_id) || empty($bed_id) || empty($data_inicio) || empty($data_fim)) {
                return [
                    "success" => false,
                    "message" => "Todos os campos são obrigatórios."
                ];
            }

            // Verificar conflitos (excluindo a própria reserva)
            if (!$this->reservaRepo->isBedAvailable($bed_id, $data_inicio, $data_fim, $id)) {
                return [
                    "success" => false,
                    "message" => "Conflito de reserva com o período informado."
                ];
            }

            $reserva = new Reserva($id, $user_id, $bed_id, $data_inicio, $data_fim);
            $success = $this->reservaRepo->update($reserva);
            
            if (!$success) {
                return [
                    "success" => false,
                    "message" => "Erro ao atualizar reserva."
                ];
            }
            
            return [
                "success" => true,
                "data" => $reserva
            ];
            
        } catch (\Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // CORRIGIR: Retornar status da exclusão
    public function delete($id) {
        try {
            $success = $this->reservaRepo->delete($id);
            return $success;
        } catch (\Exception $e) {
            return false;
        }
    }
}