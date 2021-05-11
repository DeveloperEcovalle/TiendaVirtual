<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\SerieComprobante;
use App\Sunat01TipoComprobante;
use App\TipoComprobante;
use Illuminate\Http\Request;

class TiposComprobante extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 28;
        $this->iMenuId = 31;
        $this->sPermisoListar = 'CONFTIPOSCOMLISTAR';
        $this->sPermisoInsertar = 'CONFTIPOSCOMINSERTAR';
        $this->sPermisoActualizar = 'CONFTIPOSCOMACTUALIZAR';
        $this->sPermisoEliminar = 'CONFTIPOSCOMELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.configuracion.tipos_comprobante.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.configuracion.tipos_comprobante.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.tipos_comprobante.panel_nuevo');
    }

    public function ajaxNuevoListarTiposComprobanteSunat() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
        $lstSunatTiposComprobante = [];
        if ($permiso) {
            $lstSunatTiposComprobante = Sunat01TipoComprobante::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstSunatTiposComprobante' => $lstSunatTiposComprobante];

        return response()->json($respuesta);
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.configuracion.tipos_comprobante.panel_editar');
    }

    public function ajaxEditarListarTiposComprobanteSunat() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();
        $lstSunatTiposComprobante = [];
        if ($permiso) {
            $lstSunatTiposComprobante = Sunat01TipoComprobante::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstSunatTiposComprobante' => $lstSunatTiposComprobante];

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $lstTiposComprobante = [];
        if ($permiso) {
            $lstTiposComprobante = TipoComprobante::with(['tipo_comprobante_sunat', 'series'])->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstTiposComprobante' => $lstTiposComprobante];

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
            'nombre' => 'required|string|max:255',
            'comprobante_sunat_asociado' => 'nullable',
            'serie' => 'required|string|max:4',
            'correlativo_inicio' => 'required|numeric',
            'correlativo_limite' => 'nullable|numeric',
        ]);

        $sunat_01_codigo = $request->get('comprobante_sunat_asociado');
        $valor_serie = $request->get('serie');
        if ($sunat_01_codigo) {
            $sunat_01_tipo_comprobante = Sunat01TipoComprobante::find($sunat_01_codigo);
            $regex = $sunat_01_tipo_comprobante->validacion_serie;

            $request->validate([
                'serie' => 'regex:/' . $regex . '/'
            ]);
        }

        $correlativo_inicio = intval($request->get('correlativo_inicio'));
        $correlativo_limite = intval($request->get('correlativo_limite'));

        if ($correlativo_inicio < $correlativo_limite) {
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'El correlativo l&iacute;mite debe ser mayor al correlativo inicio.';
            return response()->json($respuesta);
        }

        $fecha_reg = now()->toDateTimeString();

        $tipo_comprobante = new TipoComprobante;
        $tipo_comprobante->nombre = $request->get('nombre');
        $tipo_comprobante->sunat_01_codigo = $sunat_01_codigo;
        $tipo_comprobante->activo = 1;
        $tipo_comprobante->usuario_reg = $this->usuario->id;
        $tipo_comprobante->fecha_reg = $fecha_reg;
        $tipo_comprobante->save();

        $serie = new SerieComprobante;
        $serie->tipo_comprobante_id = $tipo_comprobante->id;
        $serie->valor = $valor_serie;
        $serie->correlativo_actual = $correlativo_inicio;
        $serie->correlativo_limite = $correlativo_limite;
        $serie->usuario_reg = $this->usuario->id;
        $serie->fecha_reg = $fecha_reg;
        $serie->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Tipo de comprobante registrado correctamente.';

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
            'nombre' => 'required|string|max:255',
            'comprobante_sunat_asociado' => 'nullable',
        ]);

        $tipo_comprobante = TipoComprobante::find($id);
        $tipo_comprobante->nombre = $request->get('nombre');
        $tipo_comprobante->sunat_01_codigo = $request->get('comprobante_sunat_asociado');
        $tipo_comprobante->usuario_act = $this->usuario->id;
        $tipo_comprobante->fecha_act = now()->toDateTimeString();
        $tipo_comprobante->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Tipo de comprobante modificado correctamente.';

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

        $valor = TipoComprobante::find($request->get('id'));
        $valor->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Tipo de comprobante eliminado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxInsertarSerie(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric',
            'serie' => 'required|string|max:4',
            'correlativo_inicio' => 'required|numeric',
            'correlativo_limite' => 'nullable|numeric',
        ]);

        $correlativo_inicio = intval($request->get('correlativo_inicio'));
        $correlativo_limite = intval($request->get('correlativo_limite'));

        if ($correlativo_inicio < $correlativo_limite) {
            $respuesta->result = Result::ERROR;
            $respuesta->mensaje = 'El correlativo l&iacute;mite debe ser mayor al correlativo inicio.';
            return response()->json($respuesta);
        }

        $tipo_comprobante_id = $request->get('id');
        $tipo_comprobante = TipoComprobante::find($tipo_comprobante_id);

        $sunat_01_codigo = $tipo_comprobante->sunat_01_codigo;
        $valor_serie = $request->get('serie');
        if ($sunat_01_codigo) {
            $sunat_01_tipo_comprobante = Sunat01TipoComprobante::find($sunat_01_codigo);
            $regex = $sunat_01_tipo_comprobante->validacion_serie;

            $request->validate([
                'serie' => 'unique:series_comprobante,valor|regex:/' . $regex . '/'
            ]);
        }

        $serie = new SerieComprobante;
        $serie->tipo_comprobante_id = $tipo_comprobante_id;
        $serie->valor = $valor_serie;
        $serie->correlativo_actual = $correlativo_inicio;
        $serie->correlativo_limite = $correlativo_limite;
        $serie->usuario_reg = $this->usuario->id;
        $serie->fecha_reg = now()->toDateTimeString();
        $serie->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Serie registrada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminarSerie(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric',
        ]);

        $serie = SerieComprobante::find($request->get('id'));
        $serie->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Serie eliminada correctamente.';

        return response()->json($respuesta);
    }
}
