<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $table = 'promociones';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'porcentaje' => 'float',
        'monto' => 'float',
    ];
}
