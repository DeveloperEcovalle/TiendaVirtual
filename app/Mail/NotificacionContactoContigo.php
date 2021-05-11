<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionContactoContigo extends Mailable {
    use Queueable, SerializesModels;

    public $apellidos_nombres;
    public $correo;
    public $fecha_nacimiento;
    public $numero_contacto;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($apellidos_nombres, $correo, $fecha_nacimiento, $numero_contacto) {
        $this->apellidos_nombres = $apellidos_nombres;
        $this->correo = $correo;
        $this->fecha_nacimiento = $fecha_nacimiento;
        $this->numero_contacto = $numero_contacto;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Nueva suscripción al Newsletter de Ecovalle')
            ->view('emails.notificacion_contacto_contigo');
    }
}
