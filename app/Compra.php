<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function ubigeo() {
        return $this->belongsTo(Ubigeo::class, 'id', 'ubigeo_id');
    }

    public function detalles() {
        return $this->hasMany(DetalleCompra::class, 'compra_id');
    }

    public function cliente(){
        return $this->belongsTo(Cliente::class, 'id', 'cliente_id');
    }
}
