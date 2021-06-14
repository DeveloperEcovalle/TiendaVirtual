<?php

namespace App\Http\Controllers\Intranet;

use App\Beneficio;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Socios extends Intranet {

    private $iPaginaId = 9;

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 24;
        $this->sPermisoListar = 'PWEBSOCIOSLISTAR';
        $this->sPermisoActualizar = 'PWEBSOCIOSACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.socios.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $pagina = null;
        if ($permiso) {
            $pagina = Pagina::find($this->iPaginaId);
        }

        $lstBeneficios = Beneficio::all();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina, 'lstBeneficios' => $lstBeneficios ];

        return response()->json($respuesta);
    }

    public function ajaxPanelEditarBeneficio(){
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.socios.panel_show');
    }

    public function ajaxActualizarBeneficio(Request $request)
    {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,svg',
            'ruta_enlace' => 'nullable',
        ]);

        $beneficio = Beneficio::find($request->get('id'));
        $imagen = $request->file('imagen');

        if ($imagen) {
            $sRutaImagenActual = str_replace('/storage', 'public', $beneficio->ruta_imagen);
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);

            $ruta_imagen = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;
            $nueva_ruta_imagen = str_replace('public/', '/storage/', $ruta_imagen);
        } else {
            $nueva_ruta_imagen = $beneficio->ruta_imagen;
        }
        $beneficio->nombre= $request->get('nombre');
        $beneficio->ruta_imagen = $nueva_ruta_imagen;
        $beneficio->ruta_enlace= $request->get('ruta_enlace');
        $beneficio->descripcion= $request->get('descripcion');
        $beneficio->update();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Beneficio actualizado correctamente.';
        $respuesta->data = ['sNuevaRutaImagen' => $nueva_ruta_imagen];

        return response()->json($respuesta);
    }

    public function ajaxPanelCrearBeneficio(){
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.socios.panel_crear');
    }

    public function ajaxCrearBeneficio(Request $request)
    {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen' => 'nullable|image|mimes:jpeg,png,svg',
            'ruta_enlace' => 'nullable',
        ]);

        $beneficio = new Beneficio();
        $imagen = $request->file('imagen');
        $beneficio->nombre= $request->get('nombre');
        $ruta_imagen = $imagen ? $imagen->store('public/empresa') : null;
        $beneficio->ruta_imagen = str_replace('public/', '/storage/', $ruta_imagen);
        $beneficio->ruta_enlace= $request->get('ruta_enlace');
        $beneficio->descripcion= $request->get('descripcion');
        $beneficio->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Beneficio creado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminarBeneficio(Request $request)
    {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $beneficio = Beneficio::find($request->id);
        if($beneficio->ruta_imagen){
            $sRutaImagenActual = str_replace('/storage', 'public', $beneficio->ruta_imagen);
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);
        }
        $beneficio->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Beneficio eliminado correctamente.';

        return response()->json($respuesta);
    }


    public function ajaxActualizarImagenPortada(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen_de_portada' => 'nullable|image|mimes:jpeg,png',
            'enlace_de_imagen_de_portada' => 'nullable',
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $imagen = $request->file('imagen_de_portada');

        if ($imagen) {
            $sRutaImagenActual = str_replace('/storage', 'public', $pagina->ruta_imagen_portada);
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);

            $ruta_imagen_portada = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;
            $nueva_ruta_imagen_portada = str_replace('public/', '/storage/', $ruta_imagen_portada);
        } else {
            $nueva_ruta_imagen_portada = $pagina->ruta_imagen_portada;
        }

        $pagina->ruta_imagen_portada = $nueva_ruta_imagen_portada;
        $pagina->enlace_imagen_portada = $request->get('enlace_de_imagen_de_portada');
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen de portada guardada correctamente.';
        $respuesta->data = ['sNuevaRutaImagen' => $nueva_ruta_imagen_portada];

        return response()->json($respuesta);
    }

    public function ajaxActualizarContenidoEspanol(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'contenido_en_espanol' => 'required'
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $pagina->contenido_espanol = $request->get('contenido_en_espanol');
        $pagina->usuario_act = $this->usuario->id;
        $pagina->fecha_act = now()->toDateTimeString();
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contenido en español modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarContenidoIngles(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'contenido_en_ingles' => 'required'
        ]);

        $pagina = Pagina::find($this->iPaginaId);
        $pagina->contenido_ingles = $request->get('contenido_en_ingles');
        $pagina->usuario_act = $this->usuario->id;
        $pagina->fecha_act = now()->toDateTimeString();
        $pagina->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Contenido en inglés modificado correctamente.';

        return response()->json($respuesta);
    }
}
