<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\LineaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Diff\Line;

class LineasProductos extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 7;
        $this->iMenuId = 36;
        $this->sPermisoListar = 'GPROLINEAPRODLISTAR';
        $this->sPermisoInsertar = 'GPROLINEAPRODINSERTAR';
        $this->sPermisoActualizar = 'GPROLINEAPRODACTUALIZAR';
        $this->sPermisoEliminar = 'GPROLINEAPRODELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.gestion_productos.lineas.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_productos.lineas.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.lineas.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.lineas.panel_editar');
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstLineas = [];
        if ($permiso) {
            $lstLineas = LineaProducto::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstLineas' => $lstLineas];

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
            'nombre_en_espanol' => 'required|string|max:100',
            'nombre_en_ingles' => 'required|string|max:100',
            'imagen' => 'nullable|image',
            'contenido_en_espanol' => 'nullable|string',
            'contenido_en_ingles' => 'nullable|string',
        ]);

        $imagen = $request->file('imagen');
        $ruta_imagen = $imagen->store('public/lineas');

        $linea = new LineaProducto;
        $linea->nombre_espanol = $request->get('nombre_en_espanol');
        $linea->nombre_ingles = $request->get('nombre_en_ingles');
        $linea->contenido_espanol = $request->get('contenido_en_espanol');
        $linea->contenido_ingles = $request->get('contenido_en_ingles');
        $linea->ruta_imagen = str_replace('public', '/storage', $ruta_imagen);
        $linea->usuario_reg = $this->usuario->id;
        $linea->fecha_reg = now()->toDateTimeString();
        $linea->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Línea de producto registrada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric',
            'nombre_en_espanol' => 'required|string|max:100',
            'nombre_en_ingles' => 'required|string|max:100',
            'imagen' => 'nullable|image',
            'contenido_en_espanol' => 'nullable|string',
            'contenido_en_ingles' => 'nullable|string',
        ]);

        $linea = LineaProducto::find($request->get('id'));

        $imagen = $request->file('imagen');
        $sRutaImagenActual = str_replace('/storage', 'public', $linea->ruta_imagen);
        if ($imagen) {
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);
        }
        $ruta_imagen = $imagen ? $imagen->store('public/lineas') : $sRutaImagenActual;

        $linea->nombre_espanol = $request->get('nombre_en_espanol');
        $linea->nombre_ingles = $request->get('nombre_en_ingles');
        $linea->contenido_espanol = $request->get('contenido_en_espanol');
        $linea->contenido_ingles = $request->get('contenido_en_ingles');
        $linea->ruta_imagen = str_replace('public', '/storage', $ruta_imagen);
        $linea->usuario_act = $this->usuario->id;
        $linea->fecha_act = now()->toDateTimeString();
        $linea->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Línea de producto modificada correctamente.';

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

        $linea = LineaProducto::find($request->get('id'));
        $linea->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Línea de producto eliminada correctamente.';


        return response()->json($respuesta);
    }
}
