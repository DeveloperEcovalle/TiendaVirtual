<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model {

    protected $table = 'personas';
    protected $primaryKey = 'id';
    public $timestamps = true;

    public function documentos() {
        return $this->hasMany(Documento::class, 'persona_id');
    }

    public function ubigeo() {
        return $this->belongsTo(Ubigeo::class, 'ubigeo_id');
    }
}
