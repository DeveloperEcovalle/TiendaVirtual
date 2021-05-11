<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model {

    protected $table = 'documentos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function tipo_documento() {
        return $this->belongsTo(Sunat06TipoDocumento::class, 'sunat_06_codigo');
    }
}
