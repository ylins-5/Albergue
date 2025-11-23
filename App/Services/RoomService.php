<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/RoomRepository.php';
require_once __DIR__ . '/../Models/Room.php';

use App\Repositories\RoomRepository;
use App\Models\Room;

class RoomService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new RoomRepository();
    }

    public function getAllRooms()
    {
        return $this->repo->findAll();
    }
 
    public function getRoomById($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("ID inválido");
        }

        $room = $this->repo->findById($id);

        if (!$room) {
            throw new \Exception("room não encontrado");
        }

        return $room;
    }

    public function createRoom($data, $file)
{
    if (empty($data['numero'])) {
        throw new \Exception("Numero é obrigatório");
    }

    $imagePath = null;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $imagePath = $this->saveImage($file);
    }

    $room = new Room(
        null,
        $data['numero'],
        $data['descricao'],
        $imagePath,
    );

    return $this->repo->create($room);
}

public function updateRoom($id, $data, $file)
{
    $room = $this->getRoomById($id);

    $room->numero = $data['numero'] ?? $room->numero;
    $room->descricao = $data['descricao'] ?? $room->descricao;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $room->imagem = $this->saveImage($file);
    }

    $this->repo->update($room);
    return $room;
}

private function saveImage($file)
{
    $folder = __DIR__ . '/../../public/uploads/rooms/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('room_') . "." . $ext;

    $fullPath = $folder . $filename;

    if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
        throw new \Exception("Erro ao salvar imagem");
    }

    return "http://localhost/albergue/public/uploads/rooms/" . $filename;
}

    public function deleteroom($id)
    {
        $this->getroomById($id);

        return $this->repo->delete($id);
    }
}
