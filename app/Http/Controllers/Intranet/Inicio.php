<?php

namespace App\Http\Controllers\Intranet;

use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Inicio extends Intranet {

    private $iPaginaId = 8;

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 42;
        $this->sPermisoListar = 'PWEBINICIOLISTAR';
        $this->sPermisoActualizar = 'PWEBINICIOACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.inicio.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $pagina = null;
        if ($permiso) {
            $pagina = Pagina::find($this->iPaginaId);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }

    public function ajaxActualizarContenidoEspanol(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'contenido_en_espanol' => 'required'
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $pagina->contenido_espanol = $request->get('contenido_en_espanol');
        $pagina->usuario_act = $this->usuario->id;
        $pagina->fecha_act = now()->toDateTimeString();
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contenido en español modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarContenidoIngles(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'contenido_en_ingles' => 'required'
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $pagina->contenido_ingles = $request->get('contenido_en_ingles');
        $pagina->usuario_act = $this->usuario->id;
        $pagina->fecha_act = now()->toDateTimeString();
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contenido en inglés modificado correctamente.';

        return response()->json($respuesta);
    }
}
