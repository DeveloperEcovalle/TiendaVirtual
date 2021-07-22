<?php

namespace App\Http\Controllers\Website;

use App\Beneficio;
use App\CategoriaBlog;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Mail\NotificacionSocioEcovalle;
use App\Pagina;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class Socios extends Website {

    protected $lstTraduccionesSocios = [
        'en' => [
            'learn_the_oportunities' => 'Learn about the opportunities we offer you to become part of the Ecovalle family',
            'last_name_and_name' => 'Last name and Name',
            'company' => 'Company',
            'city' => 'City',
            'accept_term_cond' => 'terms and conditions',
            'phone' => 'Phone',
            'join_here' => 'Join here',
        ],
        'es' => [
            'learn_the_oportunities' => 'Conoce las oportunidades que te ofrecemos para formar parte de la familia Ecovalle',
            'last_name_and_name' => 'Apellidos y Nombres',
            'company' => 'Razón social / RUC',
            'city' => 'Ciudad',
            'accept_term_cond' => 'terminos y condiciones',
            'phone' => 'Teléfono / Celular',
            'join_here' => 'Únete aquí',
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
            'lstTraduccionesSocios' => $this->lstTraduccionesSocios[$locale],
            'iPagina' => 4,
        ];

        return view('website.se_ecovalle.socios', $data);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(9);
        $beneficios = Beneficio::all();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina, 'lstBeneficios' => $beneficios];

        return response()->json($respuesta);
    }

    public function ajaxEnviar(Request $request) {
        $apellidos_nombres = $request->get('apellidos_y_nombres');
        $razon_social_ruc = $request->get('razon_social_o_ruc');
        $ciudad = $request->get('ciudad');
        $celular = $request->get('celular');

        $notificacionSocioEcovalle = new NotificacionSocioEcovalle($apellidos_nombres, $razon_social_ruc, $ciudad, $celular);
        Mail::to('ventas@ecovalle.pe')->send($notificacionSocioEcovalle);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Mensaje enviado correctamente.';

        return response()->json($respuesta);
    }
}
