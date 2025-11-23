<?php

namespace App\Services;

require_once __DIR__ . '/../Repositories/UserRepository.php';
require_once __DIR__ . '/../Models/User.php';

use App\Repositories\UserRepository;
use App\Models\User;

class UserService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new UserRepository();
    }

    public function getAllUsers()
    {
        return $this->repo->findAll();
    }

    public function getUserById($id)
    {
        if (!is_numeric($id)) {
            throw new \Exception("ID inválido");
        }

        $user = $this->repo->findById($id);

        if (!$user) {
            throw new \Exception("Usuário não encontrado");
        }

        return $user;
    }

    public function createUser($data)
    {
        if (empty($data['nome'])) {
            throw new \Exception("Nome é obrigatório");
        }

        if (empty($data['email'])) {
            throw new \Exception("Email é obrigatório");
        }

        if (empty($data['senha'])) {
            throw new \Exception("Senha é obrigatória");
        }

        if (empty($data['documento'])) {
            throw new \Exception("Cpf ou passaporte é obrigatório");
        }
        $user = new User(
            null,
            $data['nome'],
            $data['email'],
            password_hash($data['senha'], PASSWORD_DEFAULT),
            $data['documento']
        );

        return $this->repo->create($user);
    }

    public function updateUser($id, $data)
    {
        $user = $this->getUserById($id);

        $user->nome = $data['nome'] ?? $user->nome;
        $user->email = $data['email'] ?? $user->email;
        $user->senha = $data['senha'] ?? $user->senha;
        $user->documento = $data['documento'] ?? $user->documento;
        $this->repo->update($user);

        return $user;
    }

    public function deleteUser($id)
    {
        $this->getUserById($id);

        return $this->repo->delete($id);
    }
}
