<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SunatFacturaBoletaDetalle extends Model {

    protected $table = 'sunat_factura_boleta_detalle';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'cantidad' => 'float',
        'precio_venta_unitario_monto' => 'float',
        'valor_unitario' => 'float',
        'valor_venta' => 'float',
        'igv_monto_1' => 'float',
    ];
}
