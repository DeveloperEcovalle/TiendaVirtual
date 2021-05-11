<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Oferta extends Model {

    protected $table = 'ofertas';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'porcentaje' => 'float',
        'monto' => 'float',
    ];
}
