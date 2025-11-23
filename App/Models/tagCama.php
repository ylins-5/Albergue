<?php

namespace App\Models;

class TagCama {
    public $tag_id;
    public $cama_id;

    public function __construct($tag_id = null, $cama_id = null)
    {
        $this->tag_id = $tag_id;
        $this->cama_id = $cama_id;
    }
}
