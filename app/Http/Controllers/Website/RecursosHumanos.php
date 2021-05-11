<?php

namespace App\Http\Controllers\Website;

use App\CategoriaBlog;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Mail\NotificacionRecursosHumanos;
use App\Pagina;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RecursosHumanos extends Website {

    protected $lstTraduccionesRecursosHumanos = [
        'en' => [
            'learn_the_oportunities' => 'Learn about the opportunities that Ecovalle offers you to develop your skills.',
            'last_name_and_name' => 'Last name and Name',
            'subject' => 'Subject',
            'subject_position_to_apply_for' => 'Subject: Position to apply for',
            'attach_file' => 'Attach file',
            'apply_here' => 'Apply here',
        ],
        'es' => [
            'learn_the_oportunities' => 'Conoce las oportunidades que Ecovalle te ofrece para desarrollar tus capacidades',
            'last_name_and_name' => 'Apellidos y Nombres',
            'subject' => 'Subject',
            'subject_position_to_apply_for' => 'Asunto: Puesto a postular',
            'attach_file' => 'Adjuntar un archivo',
            'apply_here' => 'Postula aquí',
        ],
    ];

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesRecursosHumanos' => $this->lstTraduccionesRecursosHumanos[$locale],
            'iPagina' => 4,
        ];

        return view('website.se_ecovalle.recursos_humanos', $data);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(10);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }

    public function ajaxEnviar(Request $request) {
        $apellidos_nombres = $request->get('apellidos_y_nombres');
        $asunto = $request->get('asunto');

        $archivo = $request->file('archivo_adjunto');
        $ruta_archivo = $archivo ? $archivo->store('public/correos') : null;

        $notificacionRecursosHumanos = new NotificacionRecursosHumanos($apellidos_nombres, $asunto, $ruta_archivo);
        Mail::to('recursos.humanos@ecovalle.pe')->send($notificacionRecursosHumanos);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Mensaje enviado correctamente';

        return response()->json($respuesta);
    }
}
