<?php

namespace App\Models;

class TagQuarto {
    public $tag_id;
    public $quarto_id;

    public function __construct($tag_id = null, $quarto_id = null)
    {
        $this->tag_id = $tag_id;
        $this->quarto_id = $quarto_id;
    }
}
