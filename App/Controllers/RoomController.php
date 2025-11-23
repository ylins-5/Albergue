<?php

namespace App\Controllers;

require_once __DIR__ . '/../Services/RoomService.php';

use App\Services\RoomService;

class RoomController {
    
    private $service;

    public function __construct(){
        $this->service = new RoomService();
    }

    private function getJsonInput()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function getAll()
    {
        $Rooms = $this->service->getAllRooms();
        echo json_encode($Rooms);
    }

    public function getById($id)
    {
        try{
            $Room = $this->service->getRoomById($id);
            echo json_encode($Room);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

   public function create()
    {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents("php://input"), true);
        $file = null;
    } else {
        $data = $_POST;
        $file = $_FILES['imagem'] ?? null;
    }

    try {
        $room = $this->service->createRoom($data, $file);
        echo json_encode($room);
    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode(["error" => $e->getMessage()]);
    }
    }

    public function delete($id)
    {
        try{
            $Room = $this->service->deleteRoom($id);
            echo json_encode(["message" => "sala deletada"]);
        } catch (\Exception $e) {
            http_response_code(404);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function update($id)
    {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents("php://input"), true);
        $file = null;
    } else {
        $data = $_POST;
        $file = $_FILES['imagem'] ?? null;
    }

    try {
        $room = $this->service->updateRoom($id, $data, $file);
        echo json_encode($room);
    } catch (\Exception $e) {
        http_response_code(400);
        echo json_encode(["error" => $e->getMessage()]);
    }
    }

}