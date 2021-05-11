<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model {

    protected $table = 'blogs';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function categoria() {
        return $this->belongsTo(CategoriaBlog::class, 'categoria_id');
    }

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_reg');
    }
}
