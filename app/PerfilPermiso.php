<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PerfilPermiso extends Model {

    protected $table = 'perfiles_permisos';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
