<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model {

    protected $table = 'categorias_producto';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function productos() {
        return $this->belongsToMany(Producto::class, 'productos_categorias', 'categoria_id', 'producto_id');
    }
}
