<?php

namespace App\Http\Controllers\Website;

use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Mail\NotificacionContactanosMail;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Contactanos extends Website {

    protected $lstLocalesContactanos;

    public function __construct() {
        parent::__construct();

        $this->lstLocalesContactanos = [
            'en' => [
                'Keep in touch with us' => 'Keep in touch with us',
                'Phones Ecovalle' => 'Phones Ecovalle',
                'Address' => 'Address',
                'Follow Us' => 'Follow Us',
                'Name' => 'Name',
                'Last Name' => 'Last Name',
                'Phone' => 'Phone',
                'Subject' => 'Subject',
                'Message' => 'Message',
                'include_image' => 'If you want to include an image, you can do it here',
                'Send' => 'Send',
                'Minimum characters' => 'Minimum characters',
                'Total characters' => 'Total characters',
            ],
            'es' => [
                'Keep in touch with us' => 'Mant&eacute;n contacto con nosotros',
                'Phones Ecovalle' => 'Tel&eacute;fonos Ecovalle',
                'Follow Us' => 'Síguenos',
                'Address' => 'Dirección',
                'Name' => 'Nombres',
                'Last Name' => 'Apellidos',
                'Phone' => 'Teléfono',
                'Subject' => 'Asunto',
                'Message' => 'Mensaje',
                'include_image' => 'Si deseas incluir una imagen, puedes hacerlo aquí',
                'Send' => 'Enviar',
                'Minimum characters' => 'Caracteres como m&iacute;nimo',
                'Total characters' => 'Caracteres en total',
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $empresa = Empresa::with(['telefonos'])->first();

        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'lstLocalesContactanos' => $this->lstLocalesContactanos[$locale],

            'iPagina' => 6,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
        ];

        return view('website.contactanos', $data);
    }

    public function ajaxEnviar(Request $request) {
        $nombres = $request->get('nombres');
        $apellidos = $request->get('apellidos');
        $asunto = $request->get('asunto');
        $email = $request->get('email');
        $telefono = $request->get('telefono');
        $mensaje = $request->get('mensaje');

        $archivo = $request->file('imagen');
        $ruta_archivo = $archivo ? $archivo->store('public/correos') : null;

        $notificacionContactanosMail = new NotificacionContactanosMail($nombres, $apellidos, $asunto, $email, $telefono, $mensaje, $ruta_archivo);
        Mail::to('comunity.rrss@ecovalle.pe')->send($notificacionContactanosMail);

        //comunity.rrss@ecovalle.pe

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Mensaje enviado correctamente.';

        return response()->json($respuesta);
    }
}
