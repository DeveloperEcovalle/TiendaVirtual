<?php

namespace App\Http\Controllers\Website;

use App\CategoriaBlog;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\TelefonoEmpresa;
use Illuminate\Http\Request;
use App\Pagina;
class Blog extends Website {

    protected $lstTraduccionesBlog;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesBlog = [
            'en' => [
                'last_posts' => 'Last posts',
                'latest_content' => 'Latest content',
            ],
            'es' => [
                'last_posts' => 'Últimas publicaciones',
                'latest_content' => 'Contenido más reciente',
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesBlog' => $this->lstTraduccionesBlog[$locale],
            'iPagina' => 5,
        ];

        $vista = $request->get('v');

        if ($vista !== 'lista' && $vista !== 'publicacion' && $vista !== null) {
            return abort(404);
        }

        $vista = ($vista === null) ? 'lista' : $vista;

        $view = 'website.blog.' . $vista;
        return view($view, $data);
    }

    public function ajaxListarCategorias(Request $request) {
        $pagina = Pagina::find(13);
        $lstCategorias = CategoriaBlog::all();
        $lstUltimasPublicaciones = \App\Blog::orderBy('id', 'desc')->with('categoria')->limit(3)->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = [
            'lstCategorias' => $lstCategorias,
            'lstUltimasPublicaciones' => $lstUltimasPublicaciones,
            'pagina' => $pagina,
        ];
        return response()->json($respuesta);
    }

    public function ajaxListarPublicaciones(Request $request) {
        $iCategoriaSeleccionada = $request->get('iCategoriaSeleccionada');
        $iPaginaSeleccionada = $request->get('iPaginaSeleccionada');
        $iItemsPorPagina = $request->get('iItemsPorPagina');

        $offset = intval($iPaginaSeleccionada) * intval($iItemsPorPagina);

        if ($iCategoriaSeleccionada == 0) {
            $lstPublicaciones = \App\Blog::orderBy('id', 'desc');
        } else {
            $lstPublicaciones = \App\Blog::where('categoria_id', $iCategoriaSeleccionada)->orderBy('id', 'desc');
        }

        $iTotalPublicaciones = $lstPublicaciones->count();
        $lstPublicaciones = $lstPublicaciones->offset($offset)->limit($iItemsPorPagina)->with(['categoria', 'usuario', 'usuario.persona'])->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPublicaciones' => $lstPublicaciones, 'iTotalPublicaciones' => $iTotalPublicaciones];
        return response()->json($respuesta);
    }

    public function ajaxListarPublicacion(Request $request) {
        $sEnlace = $request->get('publicacion');
        $iId = $request->get('c');

        $publicacion = $iId ? \App\Blog::find($iId) : \App\Blog::where('enlace', $sEnlace)->first();
        $publicacion->load('usuario', 'usuario.persona');

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['publicacion' => $publicacion];
        return response()->json($respuesta);
    }

    public function ajaxListarUltimasPublicaciones(Request $request) {
        $iPublicacionId = $request->get('iPublicacionId');

        $lstUltimasPublicaciones = \App\Blog::where('id', '<>', $iPublicacionId)->orderBy('id', 'desc')->limit(3)->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUltimasPublicaciones' => $lstUltimasPublicaciones];
        return response()->json($respuesta);
    }
}
