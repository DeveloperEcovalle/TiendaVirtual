<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrecioEnvio extends Model {

    protected $table = 'precios_envio';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = ['precio' => 'float'];
}
