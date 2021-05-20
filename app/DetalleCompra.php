<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    protected $table = 'detalles_compra';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function compra() {
        return $this->belongsTo(Compra::class, 'id', 'compra_id');
    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'id', 'producto_id');
    }
}
