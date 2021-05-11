<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibroReclamaciones extends Model
{
    protected $table = 'libro_reclamos';
    protected $primaryKey = 'id';
    protected $fillable = ['codigo','nombres','apellidos','telefono','otelefono','tipo_direccion','direccion','lote','dept_int','urbanizacion','referencia','departamento','provincia','distrito','tipo_documento','numero_documento','email','monto_bien','bien_contratado','descripcion','numero_pedido','tipo_reclamo','detalle','pedido','detalleo','fecha_registro'];
    public $timestamps = false;
}
