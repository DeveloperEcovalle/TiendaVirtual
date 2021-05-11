<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model {

    protected $table = 'ventas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function ubigeo() {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }

    public function detalles() {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

}
