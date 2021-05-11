<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SunatFacturaBoleta extends Model {

    protected $table = 'sunat_factura_boleta';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'importe_total_venta' => 'float',
        'total_valor_venta_neto' => 'float',
        'total_valor_venta_gravada_monto' => 'float',
        'total_valor_venta_exonerada_monto' => 'float',
        'total_valor_venta_inafecta_monto' => 'float',
        'sumatoria_igv_monto_1' => 'float',
    ];

    public function tipo_comprobante() {
        return $this->belongsTo(TipoComprobante::class, 'tipo_comprobante_id');
    }

    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles() {
        return $this->hasMany(SunatFacturaBoletaDetalle::class, 'sunat_factura_boleta_id');
    }
}
