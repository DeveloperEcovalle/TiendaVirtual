<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Empresa;
use App\Http\Controllers\Result;
use App\Http\Controllers\Respuesta;
use Illuminate\Support\Facades\Storage;
use Exception;
class LibroReclamaciones extends Intranet
{
    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 44;
        $this->sPermisoListar = 'PWEBLIBROLISTAR';
        $this->sPermisoActualizar = 'PWEBLIBROACTUALIZAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.libro_reclamaciones.index', $data);
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $empresa = null;
        if ($permiso) {
            $empresa = Empresa::with(['telefonos'])->first();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['empresa' => $empresa];

        return response()->json($respuesta);
    }

    public function ajaxActualizarImagenLibro(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen_de_portada' => 'required|image|mimes:jpeg,png'
        ]);

        $empresa = Empresa::first();

        $imagen = $request->file('imagen_de_portada');
        if ($imagen) {
            $sRutaImagenActual = str_replace('/storage', 'public', $empresa->ruta_imagen_libro);
            $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
            Storage::disk('public')->delete($sNombreImagenActual);
            $ruta_imagen_libro = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;

            $nueva_ruta_imagen_libro = str_replace('public/', '/storage/', $ruta_imagen_libro);
            // $url_baner = public_path().$empresa->ruta_imagen_libro;
            // try
            // {
            //     unlink($url_baner);
            // }catch(Exception $e)
            // {}

            // $ruta = public_path().'/storage/empresa';
            // $fileName = uniqid().$imagen->getClientOriginalName();
            // $imagen->move($ruta,$fileName);
            // $nueva_ruta_imagen_libro = '/storage/empresa/'.$fileName;
        } else {
            $nueva_ruta_imagen_libro = $empresa->ruta_imagen_libro;
        }
        /*$sRutaImagenActual = str_replace('/storage', 'public', $empresa->ruta_imagen_libro);
        $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
        Storage::disk('public')->delete($sNombreImagenActual);
        $ruta_imagen_libro = $imagen ? $imagen->store('public/empresa') : $sRutaImagenActual;

        $nueva_ruta_imagen_libro = str_replace('public/', '/storage/', $ruta_imagen_libro);*/

        $empresa->ruta_imagen_libro = $nueva_ruta_imagen_libro;
        $empresa->update();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen de portada reemplazada correctamente.';
        $respuesta->data = ['sNuevaRutaImagen' => $nueva_ruta_imagen_libro];

        return response()->json($respuesta);
    }

    public function ajaxActualizarRuc(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'ruc_empresa' => 'required'
        ]);

        $empresa = Empresa::first();

        $ruc_empresa = $request->get('ruc_empresa');
        $empresa->ruc_empresa = $ruc_empresa;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Ruc modificado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarRazon(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'razon_social' => 'required'
        ]);

        $empresa = Empresa::first();

        $razon_social = $request->get('razon_social');
        $empresa->razon_social = $razon_social;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Razon social modificada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxActualizarMensaje(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'mensaje_libro_reclamaciones_es' => 'required',
            'mensaje_libro_reclamaciones_en' => 'required',
        ]);

        $empresa = Empresa::first();

        $mensaje_libro_reclamaciones_es = $request->get('mensaje_libro_reclamaciones_es');
        $mensaje_libro_reclamaciones_en = $request->get('mensaje_libro_reclamaciones_en');
        $empresa->mensaje_libro_reclamaciones_en = $mensaje_libro_reclamaciones_en;
        $empresa->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Mensaje modificado correctamente.';

        return response()->json($respuesta);
    }
}
