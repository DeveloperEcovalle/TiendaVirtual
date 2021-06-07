<?php

namespace App\Http\Controllers\Website;

use App\CategoriaBlog;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Mail\NotificacionServicios;
use App\Pagina;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class Servicios extends Website {

    protected $lstTraduccionesServicios = [
        'en' => [
            'we_will_help_you' => 'We will help you develop your brand',
            'last_name_and_name' => 'Last name and Name',
            'company' => 'Company',
            'city' => 'City',
            'phone' => 'Phone',
            'start_now' => 'Start now',
        ],
        'es' => [
            'we_will_help_you' => 'Nosotros te ayudaremos a desarrollar tu marca',
            'last_name_and_name' => 'Apellidos y Nombres',
            'company' => 'Razón social / RUC',
            'city' => 'Ciudad',
            'phone' => 'Teléfono / Celular',
            'start_now' => 'Iniciar ahora',
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
            'lstTraduccionesServicios' => $this->lstTraduccionesServicios[$locale],
            'iPagina' => 1, //3
        ];

        return view('website.servicios', $data);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(1);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }

    public function ajaxEnviar(Request $request) {
        $apellidos_nombres = $request->get('apellidos_y_nombres');
        $razon_social_ruc = $request->get('razon_social_o_ruc');
        $ciudad = $request->get('ciudad');
        $celular = $request->get('celular');

        $notificacionServicios = new NotificacionServicios($apellidos_nombres, $razon_social_ruc, $ciudad, $celular);

        Mail::to('ventas@ecovalle.pe')->send($notificacionServicios);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Mensaje enviado correctamente.';

        return response()->json($respuesta);
    }
}
