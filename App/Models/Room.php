<?php

namespace App\Models;

class Room
{
    public $id;
    public $numero;
    public $descricao;
    public $imagem;

    public function __construct($id = null, $numero = null, $descricao = null, $imagem = null)
    {
        $this->id = $id;
        $this->numero = $numero;
        $this->descricao = $descricao;
        $this->imagem = $imagem;
    }
}
