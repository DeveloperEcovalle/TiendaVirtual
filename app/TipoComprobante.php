<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model {

    protected $table = 'tipos_comprobante';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function tipo_comprobante_sunat() {
        return $this->belongsTo(Sunat01TipoComprobante::class, 'sunat_01_codigo');
    }

    public function series() {
        return $this->hasMany(SerieComprobante::class, 'tipo_comprobante_id');
    }
}
