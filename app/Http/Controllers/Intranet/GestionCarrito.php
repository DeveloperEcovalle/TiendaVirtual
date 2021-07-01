<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;
use App\Ubigeo;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
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
        $ubigeo->departamento = strtoupper($request->get('departamento'));
        $ubigeo->provincia = strtoupper( $request->get('provincia'));
        $ubigeo->distrito = strtoupper($request->get('distrito'));
        $ubigeo->tarifa = (float)$request->get('tarifa');
        $ubigeo->estado = $request->get('estado');
        $ubigeo->update();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Ubigeo modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxPanelNuevo() {
        return view('intranet.paginaweb.gestion_carrito.panel_nuevo');
    }

    public function ajaxInsertar(Request $request)
    {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $data = $request->all();

        $rules = [
            'id' => 'required|unique:ubigeo,id',
            'departamento' => 'required',
            'provincia' => 'required',
            'distrito' => 'required',
            'tarifa' => 'required',
        ];
        
        $message = [
            'id.required' => 'El campo id es obligatorio',
            'id.unique' => 'El campo id debe ser único',
            'departamento.required' => 'El campo departamento es obligatorio.',
            'provincia.required' => 'El campo provincia es obligatorio.',
            'distrito.required' => 'El campo distrito es obligatorio.',
            'tarifa.required' => 'El campo tarifa es obligatorio.',
        ];

        Validator::make($data, $rules, $message)->validate();

        $verificar = Ubigeo::where('departamento', strtoupper($request->get('departamento')))->where('provincia',strtoupper( $request->get('provincia')))
        ->where('distrito',strtoupper( $request->get('distrito')))->get();

        if (count($verificar) > 0) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Ubigeo ya existe';
            return response()->json($respuesta);
        }

        $ubigeo = new Ubigeo();
        $ubigeo->id = $request->get('id');
        $ubigeo->departamento = strtoupper($request->get('departamento'));
        $ubigeo->provincia = strtoupper( $request->get('provincia'));
        $ubigeo->distrito = strtoupper($request->get('distrito'));
        $ubigeo->tarifa = (float)$request->get('tarifa');
        $ubigeo->estado = $request->get('estado');
        $ubigeo->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Ubigeo insertado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminar(Request $request){
        try{
            DB::beginTransaction();
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

            $ubigeo = Ubigeo::find($request->get('id'));
            $ubigeo->delete();

            DB::commit();
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Ubigeo eliminado correctamente.';

            return response()->json($respuesta);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            $respuesta = new Respuesta;
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No puedes eliminar este ubigeo';
            return response()->json($respuesta);
        }
    }
}
