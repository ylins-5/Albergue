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
            // 1. Buscar reserva e Validar ID
            $reservaExistente = $this->reservaRepo->findById($id);
            if (!$reservaExistente) {
                return ["success" => false, "message" => "Reserva não encontrada."];
            }

            // 2. Validar campos obrigatórios
            if (empty($user_id) || empty($bed_id) || empty($data_inicio) || empty($data_fim)) {
                return ["success" => false, "message" => "Todos os campos são obrigatórios."];
            }

            // 3. Validar formato das datas
            if (!strtotime($data_inicio) || !strtotime($data_fim)) {
                return ["success" => false, "message" => "Datas inválidas."];
            }

            // 4. Verificar se a cama existe (ADICIONADO)
            $bed = $this->bedRepo->findById($bed_id);
            if (!$bed) {
                return ["success" => false, "message" => "Cama não encontrada."];
            }

            // 5. Iniciar transação (ADICIONADO - assumindo a lógica de transação no Service ou no Repo)
            // É comum o Service controlar a transação, mas aqui delegamos ao Repository para simplificar.
            // Para ter a transação completa, você precisará criar um método updateWithValidation no Repository,
            // semelhante ao createWithValidation.

            // 6. Verificar conflitos de Cama (excluindo a própria reserva)
            if (!$this->reservaRepo->isBedAvailable($bed_id, $data_inicio, $data_fim, $id)) {
                return ["success" => false, "message" => "Conflito de reserva: a cama não está disponível neste período."];
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
                // Se o update no repo falhar (e não lançar exceção)
                return ["success" => false, "message" => "Nenhuma alteração foi feita ou erro ao atualizar reserva."];
            }
            
            return ["success" => true, "data" => $reserva];
            
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // CORRIGIDO: Retornar array consistente
    public function delete($id) {
        try {
            // ATENÇÃO: É necessário implementar o método delete no ReservaRepository
            $success = $this->reservaRepo->delete($id); 
            
            if (!$success) {
                return ["success" => false, "message" => "Reserva não encontrada ou erro ao excluir."];
            }
            
            return ["success" => true, "message" => "Reserva excluída com sucesso."];
            
        } catch (Exception $e) {
            // Se o Repository lançar uma exceção de PDO
            error_log("Erro ao excluir reserva: " . $e->getMessage());
            return ["success" => false, "message" => "Erro interno ao excluir reserva."];
        }
    }

        // ADICIONAR ESTE MÉTODO
    public function getAvailableBeds($inicio, $fim) {
        // TIRE O TRY/CATCH PARA VER O ERRO REAL
        
        // 1. Verifique se as camas estão vindo do banco
        $todasCamas = $this->bedRepo->findAll();
        
        if (empty($todasCamas)) {
            die(json_encode(["DEBUG_ERROR" => "O BedRepository retornou ZERO camas. Sua tabela de camas está vazia ou o findAll falhou."]));
        }

        $camasDisponiveis = [];

        foreach ($todasCamas as $cama) {
            // 2. Verifique o que o isBedAvailable está retornando
            // Se o repositório não estiver implementado ou der erro SQL, vai estourar aqui
            $disponivel = $this->reservaRepo->isBedAvailable($cama->id, $inicio, $fim);
            
            // Debug linha a linha (opcional, se quiser ver cama por cama)
            // var_dump("Cama ID: {$cama->id} - Disponivel: " . ($disponivel ? 'SIM' : 'NAO'));

            if ($disponivel) {
                $camasDisponiveis[] = [
                    'id' => $cama->id,
                    'numero' => $cama->numero,
                    'quarto_id' => $cama->quarto_id,
                    'descricao' => $cama->descricao
                ];
            }
        }

        return $camasDisponiveis;
    }

}