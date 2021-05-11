<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model {

    protected $table = 'precios';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = ['monto' => 'float'];

}
