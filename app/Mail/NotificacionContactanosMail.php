<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionContactanosMail extends Mailable {
    use Queueable, SerializesModels;

    public $nombres;
    public $apellidos;
    public $asunto;
    public $correo;
    public $telefono;
    public $mensaje;
    public $ruta_archivo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombres, $apellidos, $asunto, $correo, $telefono, $mensaje, $ruta_archivo) {
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->asunto = $asunto;
        $this->correo = $correo;
        $this->telefono = $telefono;
        $this->mensaje = $mensaje;
        $this->ruta_archivo = $ruta_archivo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        if ($this->ruta_archivo) {
            $this->attachFromStorage($this->ruta_archivo);
        }

        return $this->subject('Nueva solicitud de contacto desde la página web')->view('emails.notificacion_contactanos');
    }
}
