<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerieComprobante extends Model {

    protected $table = 'series_comprobante';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
