<?php

namespace App\Http\Controllers\Intranet;

use App\CategoriaProducto;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoriasProductos extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 7;
        $this->iMenuId = 8;
        $this->sPermisoListar = 'GPROCATEGOLISTAR';
        $this->sPermisoInsertar = 'GPROCATEGOINSERTAR';
        $this->sPermisoActualizar = 'GPROCATEGOACTUALIZAR';
        $this->sPermisoEliminar = 'GPROCATEGOELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.gestion_productos.categorias.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_productos.categorias.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.categorias.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.categorias.panel_editar');
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstCategorias = [];
        if ($permiso) {
            $lstCategorias = CategoriaProducto::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstCategorias' => $lstCategorias];

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
            'nombre_es' => 'required|string|max:100',
            'nombre_en' => 'required|string|max:100',
            'imagen' => 'required|image',
            'imagen_de_seleccion' => 'required|image',
        ]);

        $imagen = $request->file('imagen');
        $ruta_imagen = $imagen->store('public/categorias');

        $imagen_hover = $request->file('imagen_de_seleccion');
        $ruta_imagen_hover = $imagen_hover->store('public/categorias');

        $categoria = new CategoriaProducto;
        $categoria->nombre_es = $request->get('nombre_es');
        $categoria->nombre_en = $request->get('nombre_en');
        $categoria->ruta_imagen = str_replace('public', '/storage', $ruta_imagen);
        $categoria->ruta_imagen_hover = str_replace('public', '/storage', $ruta_imagen_hover);
        $categoria->usuario_reg = $this->usuario->id;
        $categoria->fecha_reg = now()->toDateTimeString();
        $categoria->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categor&iacute;a de producto registrada correctamente.';

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

        $request->validate([
            'id' => 'required|numeric',
            'nombre_es' => 'required|string|max:100',
            'nombre_en' => 'required|string|max:100',
            'imagen' => 'nullable|image',
            'imagen_de_seleccion' => 'nullable|image',
        ]);

        $categoria = CategoriaProducto::find($request->get('id'));

        $imagen = $request->file('imagen');
        $sRutaImagenActual = str_replace('/storage', 'public', $categoria->ruta_imagen);
        if ($imagen) {
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);
        }
        $ruta_imagen = $imagen ? $imagen->store('public/categorias') : $sRutaImagenActual;

        $imagen_hover = $request->file('imagen_de_seleccion');
        $sRutaImagenHoverActual = str_replace('/storage', 'public', $categoria->ruta_imagen_hover);
        if ($imagen_hover) {
            $sNombreImagenHoverActual = str_replace('public/', '', $sRutaImagenHoverActual);
            Storage::disk('public')->delete($sNombreImagenHoverActual);
        }
        $ruta_imagen_hover = $imagen_hover ? $imagen_hover->store('public/categorias') : $sRutaImagenHoverActual;

        $categoria->nombre_es = $request->get('nombre_es');
        $categoria->nombre_en = $request->get('nombre_en');
        $categoria->ruta_imagen = str_replace('public', '/storage', $ruta_imagen);
        $categoria->ruta_imagen_hover = str_replace('public', '/storage', $ruta_imagen_hover);
        $categoria->usuario_act = $this->usuario->id;
        $categoria->fecha_act = now()->toDateTimeString();
        $categoria->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categor&iacute;a de producto modificada correctamente.';

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

        $categoria = CategoriaProducto::find($request->get('id'));

        $sRutaImagen = str_replace('/storage/', '', $categoria->ruta_imagen);
        Storage::disk('public')->delete($sRutaImagen);

        $sRutaImagenHover = str_replace('/storage/', '', $categoria->ruta_imagen_hover);
        Storage::disk('public')->delete($sRutaImagenHover);

        $categoria->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categor&iacute;a de producto eliminado correctamente.';


        return response()->json($respuesta);
    }
}
