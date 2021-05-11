<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoStock extends Model {

    protected $table = 'movimientos_stock';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
