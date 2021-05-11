<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoCategoria extends Model {

    protected $table = 'productos_categorias';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
