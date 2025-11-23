<?php

namespace App\Models;

class Bed
{
    public $id;
    public $quarto_id;
    public $numero;

    public function __construct($id = null, $quarto_id = null, $numero = null) {
        $this->id = $id;
        $this->quarto_id = $quarto_id;
        $this->numero = $numero;
    }
}