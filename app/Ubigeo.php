<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model {

    protected $table = 'ubigeos';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function personas() {
        return $this->hasMany(Persona::class, 'ubigeo_id');
    }

    public function agencias()
    {
        return $this->belongsToMany('App\Agencia')->withPivot('tarifa','direccion');
    }

}
