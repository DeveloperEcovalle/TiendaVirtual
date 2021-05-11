<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;
use App\Ubigeo;

class GestionCarrito extends Intranet
{
    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 47;
        $this->sPermisoListar = 'PWEBCARRITOLISTAR';
        $this->sPermisoInsertar = 'PWEBCARRITOINSERTAR';
        $this->sPermisoActualizar = 'PWEBCARRITOACTUALIZAR';
        $this->sPermisoEliminar = 'PWEBCARRITOELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.gestion_carrito.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.paginaweb.gestion_carrito.panel_listar');
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstUbigeo = [];
        if ($permiso) {

            $lstUbigeo = Ubigeo::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUbigeo' => $lstUbigeo];

        return response()->json($respuesta);
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.gestion_carrito.panel_editar');
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
            'departamento' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
        ]);

        $ubigeo = Ubigeo::find($id);
        $ubigeo->departamento = $request->get('departamento');
        $ubigeo->provincia = $request->get('provincia');
        $ubigeo->distrito = $request->get('distrito');
        $ubigeo->tarifa = (float)$request->get('tarifa');
        $ubigeo->estado = $request->get('estado');
        $ubigeo->update();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Ubigeo modificado correctamente.';

        return response()->json($respuesta);
    }
}
