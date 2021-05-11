<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImagenProducto extends Model {

    protected $table = 'imagenes_productos';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
