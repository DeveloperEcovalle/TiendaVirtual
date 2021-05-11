<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sunat06TipoDocumento extends Model {

    protected $table = 'sunat_06_tipos_documento';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;
}
