<?php

namespace App\Http\Controllers\Intranet;

use App\Blog;
use App\CategoriaBlog;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Exception;
class Blogs extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 20;
        $this->iMenuId = 26;
        $this->sPermisoListar = 'PWEBBLOGLISTAR';
        $this->sPermisoInsertar = 'PWEBBLOGINSERTAR';
        $this->sPermisoActualizar = 'PWEBBLOGACTUALIZAR';
        $this->sPermisoEliminar = 'PWEBBLOGELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.paginaweb.blogs.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.paginaweb.blogs.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.blogs.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.paginaweb.blogs.panel_editar');
    }

    public function ajaxListarAnios() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstAnios = [];

        if ($permiso) {
            $lstAnios = DB::table('blogs')->select(DB::raw('year(fecha_reg) as value'))->distinct()->orderBy('value', 'desc')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstAnios' => $lstAnios];

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstBlogs = [];
        if ($permiso) {
            $lFechaDesde = $request->get('lFechaDesde');
            $lFechaHasta = $request->get('lFechaHasta');

            $sFechaDesde = Carbon::createFromTimestamp(intval($lFechaDesde) / 1000)->format('Y-m-d H:i:s');
            $sFechaHasta = Carbon::createFromTimestamp(intval($lFechaHasta) / 1000)->format('Y-m-d H:i:s');

            $lstBlogs = Blog::with(['categoria', 'usuario', 'usuario.persona'])->whereBetween('fecha_reg', [$sFechaDesde, $sFechaHasta])
                ->orderBy('fecha_reg', 'desc')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstBlogs' => $lstBlogs];

        return response()->json($respuesta);
    }

    public function ajaxNuevoListarCategorias() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $lstCategorias = CategoriaBlog::all();
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstCategorias' => $lstCategorias];

        return response()->json($respuesta);
    }

    public function ajaxNuevoInsertarCategoria(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'nombre_espanol' => 'required|string',
            'nombre_ingles' => 'required|string',
        ]);

        $imagen = $request->file('banner');
        $ruta_imagen = $imagen->store('public/categorias_blog');

        $categoria = new CategoriaBlog;
        $categoria->nombre_espanol = $request->get('nombre_espanol');
        $categoria->nombre_ingles = $request->get('nombre_ingles');
        $categoria->ruta_imagen = str_replace('public/', '/storage/', $ruta_imagen);
        $categoria->usuario_reg = $this->usuario->id;
        $categoria->fecha_reg = now()->toDateTimeString();
        $categoria->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categoría registrada correctamente.';
        return response()->json($respuesta);
    }

    public function ajaxNuevoEliminarCategoria(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric',
        ]);

        $iCategoriaId = $request->get('id');

        $iCantidadPublicaciones = Blog::where('categoria_id', $iCategoriaId)->count();
        if ($iCantidadPublicaciones > 0) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Esta categoría tiene asignada más de una publicación.';
            return response()->json($respuesta);
        }

        $categoria = CategoriaBlog::find($iCategoriaId);

        $sRutaImagen = str_replace('/storage/', '', $categoria->ruta_imagen);
        Storage::disk('public')->delete($sRutaImagen);

        $categoria->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categoría eliminada correctamente.';
        return response()->json($respuesta);
    }

    public function ajaxInsertar(Request $request) {
        try{
            $this->init();

            DB::beginTransaction();
            $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();
    
            $respuesta = new Respuesta;
            $data = $request->all();
            /*$request->validate([
                'categoria' => 'required',
                'titulo' => 'required|string|max:255|unique:blogs,titulo',
                'imagen' => 'required|image|mimes:jpeg,png',
                'resumen' => 'required',
                'contenido' => 'required',
            ]);*/
            $rules = [
                'categoria' => 'required',
                'titulo' => 'required|string|max:255|unique:blogs,titulo',
                'imagen' => 'required|image|mimes:jpeg,png',
                'resumen' => 'required',
                'contenido' => 'required',
            ];
            
            $message = [
                'categoria.required' => 'El campo categoria es obligatorio.',
                'titulo.required' => 'El campo titulo es obligatorio.',
                'titulo.string' => 'El campo titulo es un texto.',
                'titulo.max' => 'El campo titulo debe tener un maximo de 255 caracteres.',
                'titulo.unique' => 'Hay un blog con el mismo titulo.',
                'imagen.required' => 'La imagen es obligatoria.',
                'imagen.image' => 'Debe ser una imagen.',
                'imagen.mimes' => 'La imagen debe ser de tipo jpeg o png.',
                'resumen.required' => 'El campo resumen es obligatorio.',
                'contenido.required' => 'El campo contenido es obligatorio.',
            ];
        
            $validator =  Validator::make($data, $rules, $message);
    
            if ($validator->fails()) {
    
                DB::rollBack();
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'Ocurrió un error de validación.';
                $respuesta->data = array('errors' => $validator->getMessageBag()->toArray());
                return response()->json($respuesta);
        
            }
    
            if ($permiso === null) {
                $respuesta->result = Result::WARNING;
                $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
                return response()->json($respuesta);
            }
    
            $imagen = $request->file('imagen');
            //$ruta_imagen_principal = $imagen->store('public/blogs');
    
            $blog = new Blog;
            $blog->categoria_id = $request->get('categoria');
            $blog->titulo = $request->get('titulo');
            if ($imagen) {
                $ruta = public_path().'/storage/blogs';
                $fileName = uniqid().$imagen->getClientOriginalName();
                $imagen->move($ruta,$fileName);
                $blog->ruta_imagen_principal = '/storage/blogs/'.$fileName;
            }
            //$blog->ruta_imagen_principal = str_replace('public/', '/storage/', $ruta_imagen_principal);
            $blog->enlace = Str::of($request->get('titulo'))->ascii()->slug('-');
            $blog->resumen = $request->get('resumen');
            $blog->contenido = $request->get('contenido');
            $blog->usuario_reg = $this->usuario->id;
            $blog->fecha_reg = now()->toDateTimeString();
            $blog->save();

            DB::commit();
    
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Publicaci&oacute;n registrada correctamente.';
    
            return response()->json($respuesta);
        }
        catch(Exception $e){
            DB::rollBack();
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Ocurrió un error de validación.';
            $respuesta->data = array('errors' => array('error' => [$e->getMessage()]));
            return response()->json($respuesta);
        }
    }

    public function ajaxEditarListarCategorias() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $lstCategorias = CategoriaBlog::all();
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstCategorias' => $lstCategorias];

        return response()->json($respuesta);
    }

    public function ajaxEditarInsertarCategoria(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'nombre_espanol' => 'required|string',
            'nombre_ingles' => 'required|string',
            'banner' => 'required|image',
        ]);

        $imagen = $request->file('banner');
        $ruta_imagen = $imagen->store('public/categorias_blog');

        $categoria = new CategoriaBlog;
        $categoria->nombre_espanol = $request->get('nombre_espanol');
        $categoria->nombre_ingles = $request->get('nombre_ingles');
        $categoria->ruta_imagen = str_replace('public/', '/storage/', $ruta_imagen);
        $categoria->usuario_reg = $this->usuario->id;
        $categoria->fecha_reg = now()->toDateTimeString();
        $categoria->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categoría registrada correctamente.';
        return response()->json($respuesta);
    }

    public function ajaxEditarEliminarCategoria(Request $request) {
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

        $iCategoriaId = $request->get('id');

        $iCantidadPublicaciones = Blog::where('categoria_id', $iCategoriaId)->count();
        if ($iCantidadPublicaciones > 0) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'Esta categoría tiene asignada más de una publicación.';
            return response()->json($respuesta);
        }

        $categoria = CategoriaBlog::find($iCategoriaId);

        $sRutaImagen = str_replace('/storage/', '', $categoria->ruta_imagen);
        Storage::disk('public')->delete($sRutaImagen);

        $categoria->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Categoría eliminada correctamente.';
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
            'categoria' => 'required',
            'titulo' => 'required|string|max:255|unique:blogs,titulo,' . $id,
            'imagen' => 'nullable|image|mimes:jpeg,png',
            'resumen' => 'required|string|max:200',
            'contenido' => 'required',
        ]);

        $blog = Blog::find($id);

        $imagen = $request->file('imagen');
        //$sRutaImagenActual = str_replace('/storage', 'public', $blog->ruta_imagen_principal);
        if ($imagen) {
            $url_baner = public_path().$blog->ruta_imagen_principal;
            try
            {
                unlink($url_baner);
            }catch(Exception $e)
            {}

            $ruta = public_path().'/storage/blogs';
            $fileName = uniqid().$imagen->getClientOriginalName();
            $imagen->move($ruta,$fileName);
            $nueva_ruta_imagen_principal = '/storage/blogs/'.$fileName;
        } else {
            $nueva_ruta_imagen_principal = $blog->ruta_imagen_principal;
        }
        // if ($imagen) {
        //     $sNombreImagenActual = str_replace('public/', '', $sRutaImagenActual);
        //     Storage::disk('public')->delete($sNombreImagenActual);
        // }
        // $ruta_imagen_principal = $imagen ? $imagen->store('public/blogs') : $sRutaImagenActual;

        $blog->categoria_id = $request->get('categoria');
        $blog->titulo = $request->get('titulo');
        //$blog->ruta_imagen_principal = str_replace('public/', '/storage/', $ruta_imagen_principal);
        $blog->ruta_imagen_principal = $nueva_ruta_imagen_principal;
        $blog->enlace = Str::of($request->get('titulo'))->ascii()->slug('-');
        $blog->resumen = $request->get('resumen');
        $blog->contenido = $request->get('contenido');
        $blog->usuario_act = $this->usuario->id;
        $blog->fecha_act = now()->toDateTimeString();
        $blog->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Publicaci&oacute;n modificada correctamente.';

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

        $banner = Blog::find($request->get('id'));
        $sRutaImagen = str_replace('/storage/', '', $banner->ruta_imagen_principal);
        Storage::disk('public')->delete($sRutaImagen);
        $banner->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Publicaci&oacute;n eliminada correctamente.';

        return response()->json($respuesta);
    }
}
