<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoProducto extends Model {

    protected $table = 'documentos_producto';
    protected $primaryKey = 'id';
    public $timestamps = false;

}
