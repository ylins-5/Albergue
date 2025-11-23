<?php

namespace App\Models;

class User
{
    public $id;
    public $nome;
    public $email;
    public $senha;
    public $documento;

    public function __construct($id = null, $nome = null, $email = null, $senha = null, $documento = null) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->documento = $documento;
    }
}