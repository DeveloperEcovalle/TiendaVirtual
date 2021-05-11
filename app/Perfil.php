<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model {

    protected $table = 'perfiles';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function permisos() {
        return $this->belongsToMany(Permiso::class, 'perfiles_permisos', 'perfil_id', 'permiso_id');
    }
}
