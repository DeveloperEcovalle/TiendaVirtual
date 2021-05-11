<?php

namespace App\Http\Controllers\Intranet;

use App\Banner;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Banners extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 21;
        $this->sPermisoListar = 'PWEBBANNERLISTAR';
        $this->sPermisoInsertar = 'PWEBBANNERINSERTAR';
        $this->sPermisoActualizar = 'PWEBBANNERACTUALIZAR';
        $this->sPermisoEliminar = 'PWEBBANNERELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.banners.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.paginaweb.banners.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.banners.panel_nuevo');
    }

    public function ajaxPanelEditar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.banners.panel_editar');
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstBanners = [];
        if ($permiso) {
            $lstBanners = Banner::orderBy('orden', 'asc')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstBanners' => $lstBanners];

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
            'imagen' => 'required|image|mimes:jpeg,png',
            'enlace' => 'nullable|url',
            'banner_en_medio_de_la_pagina' => 'nullable',
            'descripcion' => 'nullable|string|max:400'
        ]);

        $imagen = $request->file('imagen');
        $ruta_imagen = $imagen->store('public/banners');

        $medio = $request->get('banner_en_medio_de_la_pagina') ? 1 : 0;
        if ($medio === 1) {
            Banner::where('medio', 1)->update(['medio' => 0]);
        }

        $orden = intval(Banner::max('orden')) + 1;

        $banner = new Banner;
        $banner->orden = $orden;
        $banner->enlace = $request->get('enlace');
        $banner->descripcion = $request->get('descripcion');
        $banner->ruta_imagen = str_replace('public', '/storage', $ruta_imagen);
        $banner->activo = 1;
        $banner->medio = $medio;
        $banner->usuario_reg = $this->usuario->id;
        $banner->fecha_reg = now()->toDateTimeString();
        $banner->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Banner registrado correctamente.';

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
            'orden' => 'required|numeric',
            'enlace' => 'nullable|url',
            'banner_en_medio_de_la_pagina' => 'nullable',
            'descripcion' => 'nullable|string|max:400',
            'activo' => 'nullable'
        ]);

        $medio = $request->get('banner_en_medio_de_la_pagina') ? 1 : 0;
        if ($medio === 1) {
            Banner::where('medio', 1)->update(['medio' => 0]);
        }

        $banner = Banner::find($request->get('id'));
        $banner->orden = $request->get('orden');
        $banner->enlace = $request->get('enlace');
        $banner->descripcion = $request->get('descripcion');
        $banner->activo = $request->get('activo') ? 1 : 0;
        $banner->medio = $medio;
        $banner->usuario_act = $this->usuario->id;
        $banner->fecha_act = now()->toDateTimeString();
        $banner->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Banner modificado correctamente.';

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

        $banner = Banner::find($request->get('id'));
        $sRutaImagen = str_replace('/storage/', '', $banner->ruta_imagen);
        Storage::disk('public')->delete($sRutaImagen);
        $banner->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Banner eliminado correctamente.';

        return response()->json($respuesta);
    }
}
