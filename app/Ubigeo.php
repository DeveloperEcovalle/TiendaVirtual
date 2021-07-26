<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model {

    protected $table = 'ubigeo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function personas() {
        return $this->hasMany(Persona::class, 'ubigeo_id');
    }

}
