<?php

namespace App\Repositories;
require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/../Models/User.php';


use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository{

    private $PDO;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function findAll()
    {
        $statment = $this->pdo->query("SELECT * FROM users");
        $rows = $statment->fetchAll(PDO::FETCH_ASSOC);

        $users = [];

        foreach ($rows as $row) {
            $users[] = new User(
                $row['id'],
                $row['nome'],
                $row['email'],
                $row['senha']
            );
        }

        return $users;
    }

    public function findById($id)
    {
        $statment = $this->pdo->prepare("SELECT * FROM users WHERE id = ? ");
        $statment->execute([$id]);

        $row = $statment->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new User(
            $row['id'],
            $row['nome'],
            $row['email'],
            $row['senha'],
            $row['documento']);
    }

public function findByEmail($email)
{
    $statment = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
    $statment->execute([$email]);
    $row = $statment->fetch(PDO::FETCH_ASSOC);

    if (!$row) return null;

    return new User(
        $row['id'],
        $row['nome'],
        $row['email'],
        $row['senha'],
        $row['documento']
    );
}

    public function Create(User $user){

        $statment = $this->pdo->prepare("INSERT INTO users (nome, email, senha, documento) VALUES (?,?,?,?)");
        $statment->execute([$user->nome,$user->email,$user->senha, $user->documento]);

        return $this->findById($this->pdo->lastInsertId());
    }

    public function update(User $user)
    {
          $statment = $this->pdo->prepare("UPDATE users SET nome = ?, email = ?, senha = ?, documento = ? WHERE id = ?");
          return $statment->execute([$user->nome, $user->email, $user->senha, $user->documento, $user->id]);
    }

    public function delete($id)
    {
        $statment = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $statment->execute([$id]);
    }
}