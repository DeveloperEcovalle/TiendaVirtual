<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model {

    protected $table = 'clientes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function persona() {
        return $this->belongsTo(Persona::class, 'id');
    }

    public function ubigeo() {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }

    public function detalles_carrito() {
        return $this->hasMany(DetalleCarrito::class, 'cliente_id');
    }
}
