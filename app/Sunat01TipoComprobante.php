<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sunat01TipoComprobante extends Model {

    protected $table = 'sunat_01_tipos_comprobante';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    public $timestamps = false;

    public function tipos_documento() {
        return $this->belongsToMany(Sunat06TipoDocumento::class, 'sunat_01_06', 'sunat_01_codigo', 'sunat_06_codigo');
    }
}
