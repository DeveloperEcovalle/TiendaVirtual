<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model {

    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function persona() {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function perfil() {
        return $this->belongsTo(Perfil::class, 'perfil_id');
    }

}
