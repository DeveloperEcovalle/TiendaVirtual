<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoLinea extends Model {

    protected $table = 'productos_lineas';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
