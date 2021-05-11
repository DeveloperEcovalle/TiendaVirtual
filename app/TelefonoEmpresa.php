<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelefonoEmpresa extends Model {

    protected $table = 'telefonos_empresa';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
