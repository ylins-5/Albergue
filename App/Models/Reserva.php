<?php

namespace App\Models;

class Reserva
{
    public $id;
    public $user_id;
    public $bed_id;
    public $data_inicio;
    public $data_fim;

    public function __construct(
        $id = null,
        $user_id = null,
        $bed_id = null,
        $data_inicio = null,
        $data_fim = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->bed_id = $bed_id;
        $this->data_inicio = $data_inicio;
        $this->data_fim = $data_fim;
    }
}
