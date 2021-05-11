<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kardex extends Model {

    protected $table = 'kardex';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function usuario() {
        return $this->belongsTo(Usuario::class, 'usuario_reg');
    }

}
