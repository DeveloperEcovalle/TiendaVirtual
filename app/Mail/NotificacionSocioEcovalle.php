<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionSocioEcovalle extends Mailable {
    use Queueable, SerializesModels;

    public $apellidos_nombres;
    public $razon_social_ruc;
    public $ciudad;
    public $telefono_celular;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($apellidos_nombres, $razon_social_ruc, $ciudad, $telefono_celular) {
        $this->apellidos_nombres = $apellidos_nombres;
        $this->razon_social_ruc = $razon_social_ruc;
        $this->ciudad = $ciudad;
        $this->telefono_celular = $telefono_celular;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Nueva solicitud de potencial socio desde la página web')->view('emails.notificacion_socio');
    }
}
