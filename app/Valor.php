<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Valor extends Model {

    protected $table = 'valores';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
