<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model {

    protected $table = 'permisos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function menu() {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function perfilespermisos() {
        return $this->hasMany(PerfilPermiso::class, 'permiso_id');
    }

}
