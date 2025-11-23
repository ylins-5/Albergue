<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/ReservaService.php';

use App\Services\ReservaService;

class ReservaController
{
    private $service;

    public function __construct()
    {
        $this->service = new ReservaService();
    }

    public function index()
    {
        $reservas = $this->service->getAll();
        echo json_encode($reservas);
    }

    public function show($id)
    {
        $reserva = $this->service->getById($id);

        if (!$reserva) {
            http_response_code(404);
            echo json_encode(["error" => "Reserva não encontrada"]);
            return;
        }

        echo json_encode($reserva);
    }

    // --- AQUI ESTÁ A MUDANÇA PRINCIPAL (ETAPA 4) ---
    public function store()
    {
        // 1. Garante que a sessão está ativa para ler os dados
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Segurança: Verifica se o usuário está logado no servidor
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Você precisa estar logado para fazer uma reserva."]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // 3. Pega o ID do usuário DIRETAMENTE da sessão (Seguro)
        // Não pegamos mais do $data['user_id'] para evitar fraudes
        $user_id = $_SESSION['user_id'];

        // 4. Mapeamento de variáveis (Para bater com o seu alugar.js)
        // O JS manda 'quarto_id', mas seu service pode esperar 'bed_id'
        // O JS manda 'data_entrada', mas seu service espera 'data_inicio'
        $bed_id = $data['bed_id'] ?? $data['quarto_id'] ?? null;
        $data_inicio = $data['data_inicio'] ?? $data['data_entrada'] ?? null;
        $data_fim = $data['data_fim'] ?? $data['data_saida'] ?? null;
        
        // (Opcional) Se seu service precisa de número de vagas, pegue aqui:
        // $vagas = $data['vagas'] ?? 1;

        // Chama o serviço
        // Nota: Se o seu create() esperar outros parâmetros, ajuste aqui.
        $result = $this->service->create($user_id, $bed_id, $data_inicio, $data_fim);

        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }

        http_response_code(201); // Created
        echo json_encode($result["data"]);
    }

    public function update($id)
    {
        // Também protegemos o update
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Não autorizado"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // No update, talvez você queira permitir mudar o usuário da reserva ou não.
        // Aqui mantive pegando do input, mas o ideal seria validar se a reserva pertence ao usuário logado.
        $user_id = $data['user_id'] ?? $_SESSION['user_id']; 
        $bed_id = $data['bed_id'] ?? null;
        $data_inicio = $data['data_inicio'] ?? null;
        $data_fim = $data['data_fim'] ?? null;

        $result = $this->service->update($id, $user_id, $bed_id, $data_inicio, $data_fim);

        if (!$result["success"]) {
            http_response_code(400);
            echo json_encode(["error" => $result["message"]]);
            return;
        }

        echo json_encode($result["data"]);
    }

    public function destroy($id)
    {
        // Proteção básica
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return;
        }

        $this->service->delete($id);
        echo json_encode(["success" => true]);
    }

    public function availableBeds()
    {
        $inicio = $_GET['inicio'] ?? null;
        $fim = $_GET['fim'] ?? null;

        if (!$inicio || !$fim) {
            http_response_code(400);
            echo json_encode(["error" => "Envie inicio e fim"]);
            return;
        }

        $beds = $this->service->getAvailableBeds($inicio, $fim);

        echo json_encode($beds);
    }
}