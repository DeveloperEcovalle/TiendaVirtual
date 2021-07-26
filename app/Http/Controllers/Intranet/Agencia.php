<?php

namespace App\Http\Controllers\Intranet;

use App\Agencia as AppAgencia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;

class Agencia extends Intranet
{
    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 48;
        $this->sPermisoListar = 'CONFAGENCIALISTAR';
        $this->sPermisoInsertar = 'CONFAGENCIAINSERTAR';
        $this->sPermisoActualizar = 'CONFAGENCIAINSERTAR';
        $this->sPermisoEliminar = 'CONFAGENCIAELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.agencias.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.configuracion.agencias.panel_listar');
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $lstAgencias = [];
        if ($permiso) {
            $lstAgencias = AppAgencia::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstAgencias' => $lstAgencias];

        return response()->json($respuesta);
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.agencias.panel_editar');
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
            'nombre' => 'required',
            'estado' => 'required',
        ]);

        $agencia = new AppAgencia();
        $agencia->nombre = strtoupper($request->get('nombre'));
        $agencia->descripcion = $request->get('descripcion');
        $agencia->estado = $request->get('estado');
        $agencia->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Agencia registrada correctamente.';

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
            'id' => 'required',
            'nombre' => 'required',
            'estado' => 'required',
        ]);

        $agencia = AppAgencia::find($id);
        $agencia->nombre = strtoupper($request->get('nombre'));
        $agencia->descripcion = $request->get('descripcion');
        $agencia->estado = $request->get('estado');
        $agencia->update();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Agencia modificada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.agencias.panel_nuevo');
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

        $valor = AppAgencia::find($request->get('id'));
        $valor->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Agencia eliminada correctamente.';

        return response()->json($respuesta);
    }
}
