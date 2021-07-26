<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    protected $table = 'agencias';
    protected $primaryKey = 'id';
    public $timestamps = false;
}
