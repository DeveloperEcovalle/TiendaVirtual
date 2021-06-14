<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    protected $table = 'beneficios';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
