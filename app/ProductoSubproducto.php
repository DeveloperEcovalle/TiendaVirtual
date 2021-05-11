<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoSubproducto extends Model {

    protected $table = 'productos_subproductos';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
