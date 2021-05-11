<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model {

    protected $table = 'proveedores';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function persona() {
        return $this->belongsTo(Persona::class, 'id');
    }

    public function ubigeo() {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }
}