<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Perfil;
use App\Persona;
use App\PrecioEnvio;
use App\Ubigeo;
use App\Usuario;
use Illuminate\Http\Request;

class PreciosEnvio extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 11;
        $this->iMenuId = 44;
        $this->sPermisoListar = 'GINVPRECIOSENVIOLISTAR';
        $this->sPermisoInsertar = 'GINVPRECIOSENVIOINSERTAR';
        $this->sPermisoActualizar = 'GINVPRECIOSENVIOACTUALIZAR';
        $this->sPermisoEliminar = 'GINVPRECIOSENVIOELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.gestion_inventario.precios_envio.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_inventario.precios_envio.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_inventario.precios_envio.panel_nuevo');
    }

    public function ajaxListarUbigeo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
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

        return view('intranet.gestion_inventario.precios_envio.panel_editar');
    }

    public function ajaxEditarListarPerfiles() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        $lstPerfiles = [];
        if ($permiso) {
            $lstPerfiles = Perfil::orderBy('id_interno')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPerfiles' => $lstPerfiles];

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $lstPreciosEnvio = [];
        if ($permiso) {
            $lstPreciosEnvio = PrecioEnvio::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPreciosEnvio' => $lstPreciosEnvio];

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
            'departamento' => 'required|string',
            'provincia' => 'string',
            'distrito' => 'string',
            'precio' => 'required|numeric|min:0',
        ]);

        $sProvincia = $request->get('provincia');
        $sDistrito = $request->get('distrito');

        $precioEnvio = new PrecioEnvio;
        $precioEnvio->departamento = $request->get('departamento');
        $precioEnvio->provincia = $sProvincia === null ? '' : $sProvincia;
        $precioEnvio->distrito = $sDistrito === null ? '' : $sDistrito;
        $precioEnvio->precio = $request->get('precio');
        $precioEnvio->usuario_reg = $this->usuario->id;
        $precioEnvio->fecha_reg = now()->toDateTimeString();
        $precioEnvio->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Precio de envío registrado correctamente.';
        $respuesta->data = $precioEnvio->id;

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
            'departamento' => 'required|string',
            'provincia' => 'string',
            'distrito' => 'string',
            'precio' => 'required|numeric|min:0',
        ]);

        $sProvincia = $request->get('provincia');
        $sDistrito = $request->get('distrito');

        $precioEnvio = PrecioEnvio::find($id);
        $precioEnvio->departamento = $request->get('departamento');
        $precioEnvio->provincia = $sProvincia === null ? '' : $sProvincia;
        $precioEnvio->distrito = $sDistrito === null ? '' : $sDistrito;
        $precioEnvio->precio = $request->get('precio');
        $precioEnvio->usuario_act = $this->usuario->id;
        $precioEnvio->fecha_act = now()->toDateTimeString();
        $precioEnvio->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Precio de envío modificado correctamente.';

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

        $request->validate(['id' => 'required|numeric']);

        $valor = PrecioEnvio::find($request->get('id'));
        $valor->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Precio de envío eliminado correctamente.';

        return response()->json($respuesta);
    }
}
