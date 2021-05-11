<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VentaComprobanteCliente extends Mailable {
    use Queueable, SerializesModels;

    public $sunatFacturaBoleta;
    public $sRutaComprobanteXml;
    public $sRutaComprobantePdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($sunatFacturaBoleta, $sRutaComprobanteXml, $sRutaComprobantePdf) {
        $this->sunatFacturaBoleta = $sunatFacturaBoleta;
        $this->sRutaComprobanteXml = $sRutaComprobanteXml;
        $this->sRutaComprobantePdf = $sRutaComprobantePdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        $sSerieNroComprobante = $this->sunatFacturaBoleta->serie_comprobante . '-' . $this->sunatFacturaBoleta->nro_comprobante;
        return $this->subject('DETALLE DE SU PEDIDO ' . $sSerieNroComprobante)
            ->attachFromStorageDisk('public', $this->sRutaComprobanteXml)
            ->attachFromStorageDisk('public', $this->sRutaComprobantePdf)
            ->view('emails.venta_comprobante_cliente');
    }
}
