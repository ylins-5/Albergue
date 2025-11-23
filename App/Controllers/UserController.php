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
        $users = $this->service->getAllUsers();
        echo json_encode($users);
    }

    public function getById($id)
    {
        try{
            $user = $this->service->getUserById($id);
            echo json_encode($user);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function create() 
    {
        $data = $this->getJsonInput();

        try{
            $user = $this->service->createUser($data);
            echo json_encode($user);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]); //<-- linha 48
        }
    }

    public function update($id)
    {
        $data = $this->getJsonInput();
        try{
            $user = $this->service->updateUser($id, $data);
            echo json_encode($user);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try{
            $user = $this->service->deleteUser($id);
            echo json_encode(["message" => "usuario deletado"]);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}