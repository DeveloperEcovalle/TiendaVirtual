<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;

class GuiaCompras extends Website
{
    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'iPagina' => -1,
        ];

        return view('website.guia_compras', $data);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(12);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }
}
