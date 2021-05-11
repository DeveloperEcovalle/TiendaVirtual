<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionRecursosHumanos extends Mailable {
    use Queueable, SerializesModels;

    public $apellidos_nombres;
    public $asunto;
    public $ruta_archivo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($apellidos_nombres, $asunto, $ruta_archivo) {
        $this->apellidos_nombres = $apellidos_nombres;
        $this->asunto = $asunto;
        $this->ruta_archivo = $ruta_archivo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {
        return $this->subject('Nuevo postulante registrado desde la página web')
            ->attachFromStorage($this->ruta_archivo)
            ->view('emails.notificacion_recursos_humanos');
    }
}
