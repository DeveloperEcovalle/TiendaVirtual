<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Valor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Valores extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 35;
        $this->sPermisoListar = 'PWEBVALORLISTAR';
        $this->sPermisoInsertar = 'PWEBVALORINSERTAR';
        $this->sPermisoActualizar = 'PWEBVALORACTUALIZAR';
        $this->sPermisoEliminar = 'PWEBVALORELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.valores.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.paginaweb.valores.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.valores.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.valores.panel_editar');
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstValores = [];
        if ($permiso) {
            $lstValores = Valor::orderBy('orden')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstValores' => $lstValores];

        return response()->json($respuesta);
    }

    public function ajaxInsertar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'nombre_es' => 'required|string|max:255|unique:certificaciones,nombre_es',
            'nombre_en' => 'required|string|max:255|unique:certificaciones,nombre_en',
            'descripcion_es' => 'required|string',
            'descripcion_en' => 'required|string',
        ]);

        $orden = intval(Valor::max('orden')) + 1;

        $valor = new Valor;
        $valor->nombre_es = $request->get('nombre_es');
        $valor->nombre_en = $request->get('nombre_en');
        $valor->descripcion_es = $request->get('descripcion_es');
        $valor->descripcion_en = $request->get('descripcion_en');
        $valor->orden = $orden;
        $valor->usuario_reg = $this->usuario->id;
        $valor->fecha_reg = now()->toDateTimeString();
        $valor->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Valor registrado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $id = $request->get('id');

        $request->validate([
            'id' => 'required|numeric',
            'orden' => 'required|numeric',
            'nombre_es' => 'required|string|max:255|unique:certificaciones,nombre_es,' . $id,
            'nombre_en' => 'required|string|max:255|unique:certificaciones,nombre_en,' . $id,
            'descripcion_es' => 'required|string',
            'descripcion_en' => 'required|string',
        ]);

        $valor = Valor::find($id);

        $valor->nombre_es = $request->get('nombre_es');
        $valor->nombre_en = $request->get('nombre_en');
        $valor->descripcion_es = $request->get('descripcion_es');
        $valor->descripcion_en = $request->get('descripcion_en');
        $valor->orden = $request->get('orden');
        $valor->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Valor modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoEliminar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $valor = Valor::find($request->get('id'));
        $valor->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Valor eliminado correctamente.';

        return response()->json($respuesta);
    }
}
