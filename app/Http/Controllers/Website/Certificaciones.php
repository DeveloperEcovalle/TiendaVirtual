<?php

namespace App\Http\Controllers\Website;

use App\CategoriaBlog;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;

class Certificaciones extends Website {

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'iPagina' => 1,
        ];

        return view('website.nosotros.certificaciones2', $data);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(4);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }
}
