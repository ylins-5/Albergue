<?php

namespace App\Models;

class Tag
{
    public $id;
    public $nome;
    public $tipo;

    public function __construct($id = null, $nome = null, $tipo = null)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->tipo = $tipo;
    }
}
