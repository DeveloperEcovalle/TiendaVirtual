<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCarrito extends Model {

    protected $table = 'detalles_carrito';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
