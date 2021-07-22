<?php

namespace App\Http\Controllers\Intranet;

use App\Agencia as AppAgencia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;
use App\Ubigeo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ItemAutocompletar;

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

    public function ajaxListarUbigeo($id) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $lstUbigeo = [];
        if ($permiso) {
            $lstUbigeo = Ubigeo::all();
            $lstDestinos = DB::table('agencia_ubigeo')
            ->join('ubigeos', 'ubigeos.id', '=', 'agencia_ubigeo.ubigeo_id')
            ->select('ubigeos.*', 'agencia_ubigeo.id as idDestino', 'agencia_ubigeo.tarifa as tarifaDestino', 'agencia_ubigeo.direccion')
            ->where('agencia_ubigeo.agencia_id',$id)->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUbigeo' => $lstUbigeo, 'lstDestinos' => $lstDestinos];

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

    public function ajaxInsertarDestino(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required',
            'departamento' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
            'tarifa' => 'required',
        ]);

        $ubigeo = Ubigeo::where('departamento',$request->departamento)->where('provincia',$request->provincia)->where('distrito',$request->distrito)->first();

        if(!empty($ubigeo))
        {
            DB::table('agencia_ubigeo')->updateOrInsert(
                [
                    'ubigeo_id' => $ubigeo->id, 
                    'agencia_id' => $request->id,
                ],
                [
                    'tarifa' => $request->tarifa,
                    'direccion' => $request->direccion,
                ]
            );

            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Destino registrado correctamente.';
    
            return response()->json($respuesta);
        }
        else
        {
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'Destino no encontrado.';
    
            return response()->json($respuesta);
        }
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

    public function ajaxEditarAutocompletarUbigeo(Request $request)
    {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $lstUbigeo = [];
        if ($permiso) {
            $texto = '%' . $request->get('texto') . '%';
            $lstUbigeo = Ubigeo::where('departamento', 'like', $texto)->orWhere('provincia', 'like', $texto)->orWhere('distrito', 'like', $texto)->limit(5)->get();
        }

        $data = [];
        foreach ($lstUbigeo as $ubigeo) {
            $item_autocompletar = new ItemAutocompletar;
            $item_autocompletar->label = $ubigeo->departamento.' - '.$ubigeo->provincia.' - '.$ubigeo->distrito;
            $item_autocompletar->value = $ubigeo->departamento.' - '.$ubigeo->provincia.' - '.$ubigeo->distrito;
            $item_autocompletar->entidad = $ubigeo;

            array_push($data, $item_autocompletar);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }
}
