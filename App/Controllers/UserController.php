<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/UserService.php';

use App\Services\UserService;

class UserController {
    
    private $service;

    public function __construct(){
        $this->service = new UserService();
    }

    private function getJsonInput()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function getAll()
    {
        header('Content-Type: application/json');
        $users = $this->service->getAllUsers();
        echo json_encode($users);
    }

    public function getById($id)
    {
        header('Content-Type: application/json');
        try{
            $user = $this->service->getUserById($id);
            echo json_encode($user);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function login()
    {
        header('Content-Type: application/json');
        $data = $this->getJsonInput();

        try {
            if (empty($data['email']) || empty($data['senha'])) {
                throw new \Exception("Email e senha são obrigatórios.");
            }

            $user = $this->service->login($data['email'], $data['senha']);
            
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_nome'] = $user->nome;
            $_SESSION['user_email'] = $user->email;

            echo json_encode([
                "message" => "Login realizado com sucesso",
                "user" => [
                    "id" => $user->id,
                    "nome" => $user->nome,
                    "email" => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            http_response_code(401); 
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function checkSession()
    {
        header('Content-Type: application/json');
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                "authenticated" => true,
                "user" => [
                    "id" => $_SESSION['user_id'],
                    "nome" => $_SESSION['user_nome'],
                    "email" => $_SESSION['user_email']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["authenticated" => false]);
        }
    }

    public function logout()
    {
        header('Content-Type: application/json');
        session_destroy();
        echo json_encode(["message" => "Logout realizado"]);
    }

    public function create() 
    {
        header('Content-Type: application/json');
        $data = $this->getJsonInput();

        try{
            $user = $this->service->createUser($data);
            http_response_code(201);
            echo json_encode($user);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(["error" => "Não autorizado"]);
            return;
        }

        $realId = $_SESSION['user_id'];
        $data = $this->getJsonInput();

        try{
            $user = $this->service->updateUser($realId, $data);
            $_SESSION['user_nome'] = $user->nome;
            $_SESSION['user_email'] = $user->email;

            echo json_encode($user);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        header('Content-Type: application/json');
        try{
            $user = $this->service->deleteUser($id);
            echo json_encode(["message" => "usuario deletado"]);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
