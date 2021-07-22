<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    protected $table = 'calificaciones';
    protected $primaryKey = 'id';

    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id'); //oro
    }

    public function producto() {
        return $this->belongsTo(Producto::class, 'producto_id', 'id'); //oro
    }
}
