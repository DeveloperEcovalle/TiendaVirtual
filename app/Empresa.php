<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model {

    protected $table = 'empresa';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function secciones_quienes_somos() {
        return $this->hasMany(SeccionQuienesSomos::class, 'empresa_id');
    }

    public function valores() {
        return $this->hasMany(Valor::class, 'empresa_id');
    }

    public function telefonos() {
        return $this->hasMany(TelefonoEmpresa::class, 'empresa_id');
    }

}
