<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Menu;
use App\Perfil;
use App\PerfilPermiso;
use Illuminate\Http\Request;

class Perfiles extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 29;
        $this->sPermisoListar = 'CONFPERUSULISTAR';
        $this->sPermisoInsertar = 'CONFPERUSUINSERTAR';
        $this->sPermisoActualizar = 'CONFPERUSUACTUALIZAR';
        $this->sPermisoEliminar = 'CONFPERUSUELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.perfiles.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.configuracion.perfiles.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.perfiles.panel_nuevo');
    }

    public function ajaxNuevoListarPermisos() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        $lstMenus = [];
        if ($permiso) {
            $lstMenus = Menu::whereHas('permisos')->with(['permisos'])->orderBy('orden')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstMenus' => $lstMenus];

        return response()->json($respuesta);
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.perfiles.panel_editar');
    }

    public function ajaxEditarListarPermisos(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        $lstMenus = [];
        if ($permiso) {
            global $iPerfilId;
            $iPerfilId = $request->get('iPerfilId');
            $lstMenus = Menu::whereHas('permisos')->with(['permisos', 'permisos.perfilespermisos' => function ($perfilpermiso) {
                $perfilpermiso->where('perfil_id', $GLOBALS['iPerfilId']);
            }])->orderBy('orden')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstMenus' => $lstMenus];

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $lstPerfiles = [];
        if ($permiso) {
            $lstPerfiles = Perfil::orderBy('id_interno')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPerfiles' => $lstPerfiles];

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
            'nombre' => 'required|string|max:100|unique:perfiles,nombre',
            'permisos' => 'required',
        ]);

        $id_interno = intval(Perfil::max('id_interno')) + 1;

        $fecha_reg = now()->toDateTimeString();

        $perfil = new Perfil;
        $perfil->nombre = $request->get('nombre');
        $perfil->id_interno = $id_interno;
        $perfil->usuario_reg = $this->usuario->id;
        $perfil->fecha_reg = $fecha_reg;
        $perfil->save();

        $permisos = $request->get('permisos');
        $perfiles_permisos = array();
        foreach ($permisos as $permiso_id) {
            array_push($perfiles_permisos, array(
                'perfil_id' => $perfil->id,
                'permiso_id' => $permiso_id,
                'usuario_reg' => $this->usuario->id,
                'fecha_reg' => $fecha_reg,
            ));
        }

        PerfilPermiso::insert($perfiles_permisos);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Perfil registrado correctamente.';

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
            'nombre' => 'required|string|max:100|unique:perfiles,nombre,' . $id,
            'permisos' => 'required',
        ]);

        $fecha_act = now()->toDateTimeString();

        $perfil = Perfil::find($id);
        $perfil->nombre = $request->get('nombre');
        $perfil->usuario_act = $this->usuario->id;
        $perfil->fecha_act = $fecha_act;
        $perfil->save();

        PerfilPermiso::where('perfil_id', $perfil->id)->delete();

        $permisos = $request->get('permisos');
        $perfiles_permisos = array();
        foreach ($permisos as $permiso_id) {
            array_push($perfiles_permisos, array(
                'perfil_id' => $perfil->id,
                'permiso_id' => $permiso_id,
                'usuario_reg' => $this->usuario->id,
                'fecha_reg' => $fecha_act,
            ));
        }

        PerfilPermiso::insert($perfiles_permisos);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Perfil modificado correctamente.';

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

        $valor = Perfil::find($request->get('id'));
        $valor->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Perfil eliminado correctamente.';

        return response()->json($respuesta);
    }
}
