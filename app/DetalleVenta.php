<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model {

    protected $table = 'detalles_venta';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function precio() {
        return $this->hasOne(Precio::class, 'precio_id');
    }

}
