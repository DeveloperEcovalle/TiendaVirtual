<?php

namespace App\Http\Controllers\Website;

use App\Certificacion;
use App\Empresa;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;

class Nosotros extends Website {

    protected $lstLocalesQuienesSomos;

    public function quienesSomos(Request $request) {
        $empresa = Empresa::with(['valores'])->first();

        $this->lstLocalesQuienesSomos = [
            'en' => [
                'Ethos' => 'Ethos',
            ],
            'es' => [
                'Ethos' => 'Valores',
            ]
        ];

        $locale = $request->session()->get('locale');

        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'lstLocalesQuienesSomos' => $this->lstLocalesQuienesSomos[$locale],
            'iPagina' => 1,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
        ];

        return view('website.nosotros.quienes_somos', $data);
    }
}
