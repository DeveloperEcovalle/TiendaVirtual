<div style="max-width: 600px; width: 100%; margin-left: auto; margin-right: auto">
    <div style="padding: 15px; text-align: center">
        <img src="{{ $message->embed('https://ecovalle.pe/img/logo_ecovalle.svg') }}" style="max-width: 100%">
    </div>
    <div style="padding: 15px">
        <h3>DETALLE DE SU ORDEN</h3>
        @foreach($sunatFacturaBoleta->detalles as $detalle)
            <div style="display: block; padding: 4px; border-bottom: #0c5460 1px solid">
                <span>{{ $detalle->cantidad . ' ' . $detalle->descripcion }}</span>
                <span style="float: right">{{ number_format($detalle->cantidad * $detalle->precio_venta_unitario_monto, 2, '.', '') }}</span>
            </div>
        @endforeach
    </div>
    <div style="padding: 15px">
        <h4>TOTAL PEDIDO <span style="float: right">S/ {{ number_format($sunatFacturaBoleta->importe_total_venta, 2, '.', '') }}</span></h4>
    </div>
    <div style="padding: 15px"><b>AGROENSANCHA S.R.L</b></div>
</div>
