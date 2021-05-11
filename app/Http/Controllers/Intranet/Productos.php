<?php

namespace App\Http\Controllers\Intranet;

use App\CategoriaProducto;
use App\DocumentoProducto;
use App\Http\Controllers\ItemAutocompletar;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\ImagenProducto;
use App\LineaProducto;
use App\Oferta;
use App\Precio;
use App\Producto;
use App\ProductoCategoria;
use App\ProductoLinea;
use App\ProductoSubproducto;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class Productos extends Intranet {

    public function init() {
        parent::init();

        $this->iModuloId = 7;
        $this->iMenuId = 9;
        $this->sPermisoListar = 'GPROPRODUCTOSLISTAR';
        $this->sPermisoInsertar = 'GPROPRODUCTOSINSERTAR';
        $this->sPermisoActualizar = 'GPROPRODUCTOSACTUALIZAR';
        $this->sPermisoEliminar = 'GPROPRODUCTOSELIMINAR';
    }

    public function index() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();
        $data = ['iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];

        if ($permiso === null) {
            return view('intranet.layout_left_sin_permiso', $data);
        }

        return view('intranet.gestion_productos.productos.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_productos.productos.panel_listar');
    }

    public function ajaxPanelNuevo() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.productos.panel_nuevo');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.productos.panel_editar');
    }

    public function ajaxListar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstProductos = [];
        if ($permiso) {
            $lstProductos = Producto::with(['categorias', 'lineas', 'documentos', 'subproductos', 'subproductos.precio_actual', 'subproductos.oferta_vigente'])->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstProductos' => $lstProductos];

        return response()->json($respuesta);
    }

    public function ajaxNuevoListarData(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

        $lstLineas = [];
        $lstCategorias = [];
        if ($permiso) {
            $lstLineas = LineaProducto::orderBy('nombre_espanol', 'asc')->get();
            $lstCategorias = CategoriaProducto::orderBy('nombre_es', 'asc')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstLineas' => $lstLineas, 'lstCategorias' => $lstCategorias];

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
            'nombre_en_espanol' => 'required|string|max:500',
            'nombre_en_ingles' => 'required|string|max:500',
            'beneficios_en_espanol' => 'required|string',
            'beneficios_en_ingles' => 'required|string',
            'descripcion_en_espanol' => 'required|string',
            'descripcion_en_ingles' => 'required|string',
            'modo_de_uso_en_espanol' => 'required|string',
            'modo_de_uso_en_ingles' => 'required|string',
            'nombres_documentos' => 'nullable',
            'documentos' => 'nullable',
            'categorias' => 'required',
            'lineas' => 'required',
        ]);

        $fecha_reg = now()->toDateTimeString();

        $producto = new Producto;
        $producto->nombre_es = $request->get('nombre_en_espanol');
        $producto->nombre_en = $request->get('nombre_en_ingles');
        $producto->beneficios_es = $request->get('beneficios_en_espanol');
        $producto->beneficios_en = $request->get('beneficios_en_ingles');
        $producto->descripcion_es = $request->get('descripcion_en_espanol');
        $producto->descripcion_en = $request->get('descripcion_en_ingles');
        $producto->modo_uso_es = $request->get('modo_de_uso_en_espanol');
        $producto->modo_uso_en = $request->get('modo_de_uso_en_ingles');
        $producto->usuario_reg = $this->usuario->id;
        $producto->fecha_reg = $fecha_reg;
        $producto->save();

        $documentos = $request->file('documentos');
        if ($documentos) {
            $rutas_documentos = array();
            foreach ($documentos as $documento) {
                $ruta_documento = $documento->store('public/productos');
                array_push($rutas_documentos, $ruta_documento);
            }

            $nombres_documentos = $request->get('nombres_documentos');
            $documentos_producto = array();
            foreach ($nombres_documentos as $i => $nombre_documento) {
                array_push($documentos_producto, array(
                    'producto_id' => $producto->id,
                    'nombre_descarga' => $nombre_documento,
                    'ruta_archivo' => str_replace('public', '/storage', $rutas_documentos[$i]),
                ));
            }
            DocumentoProducto::insert($documentos_producto);
        }

        $categorias = $request->get('categorias');
        $productos_categorias = array();
        foreach ($categorias as $categoria_id) {
            array_push($productos_categorias, array(
                'producto_id' => $producto->id,
                'categoria_id' => $categoria_id,
                'usuario_reg' => $this->usuario->id,
                'fecha_reg' => $fecha_reg
            ));
        }
        ProductoCategoria::insert($productos_categorias);

        $lineas = $request->get('lineas');
        $productos_lineas = array();
        foreach ($lineas as $linea_id) {
            array_push($productos_lineas, array(
                'producto_id' => $producto->id,
                'linea_id' => $linea_id,
                'usuario_reg' => $this->usuario->id,
                'fecha_reg' => $fecha_reg
            ));
        }
        ProductoLinea::insert($productos_lineas);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Producto registrado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEditarListarData(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $lstLineas = [];
        $lstCategorias = [];
        if ($permiso) {
            $lstLineas = LineaProducto::orderBy('nombre_espanol', 'asc')->get();
            $lstCategorias = CategoriaProducto::orderBy('nombre_es', 'asc')->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstLineas' => $lstLineas, 'lstCategorias' => $lstCategorias];

        return response()->json($respuesta);
    }

    public function ajaxEditarAutocompletarProductos(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $lstProductos = [];
        if ($permiso) {
            $texto = '%' . $request->get('texto') . '%';
            $lstProductos = Producto::where('nombre_es', 'like', $texto)->doesntHave('subproductos')->with(['precio_actual', 'oferta_vigente'])->get();
        }

        $data = [];
        foreach ($lstProductos as $producto) {
            $item_autocompletar = new ItemAutocompletar;
            $item_autocompletar->label = $producto->nombre_es;
            $item_autocompletar->value = $producto->nombre_es;
            $item_autocompletar->entidad = $producto;

            array_push($data, $item_autocompletar);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }

    public function ajaxEditarInsertarSubproducto(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $iProductoId = $request->get('iProductoId');
        $iSubproductoId = $request->get('iSubproductoId');

        $producto_subproducto = new ProductoSubproducto;
        $producto_subproducto->producto_id = $iProductoId;
        $producto_subproducto->subproducto_id = $iSubproductoId;
        $producto_subproducto->usuario_reg = $this->usuario->id;
        $producto_subproducto->fecha_reg = now()->toDateTimeString();
        $producto_subproducto->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Subproducto registrado correctamente.';
        return response()->json($respuesta);
    }

    public function ajaxEliminarDocumento(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $id = $request->get('id');
        $documento_producto = DocumentoProducto::find($id);
        $sRutaDocumento = str_replace('/storage', 'public', $documento_producto->ruta_archivo);
        Storage::disk('public')->delete($sRutaDocumento);

        $documento_producto->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Documento eliminado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxListarImagenes(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para ver esta informaci&oacute;n';
            return response()->json($respuesta);
        }

        $producto_id = $request->get('id');
        $lstImagenes = ImagenProducto::where('producto_id', $producto_id)->get();

        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstImagenes' => $lstImagenes];
        return response()->json($respuesta);
    }

    public function ajaxInsertarImagen(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'imagen' => 'image|required|max:3000'
        ]);

        $imagen = $request->file('imagen');

        if (!$imagen) {
            $respuesta->result = Result::ERROR;
            return response()->json($respuesta, 400);
        }

        $ruta_imagen = $imagen->store('public/productos');

        $imagen_producto = new ImagenProducto;
        $imagen_producto->producto_id = $request->get('producto_id');
        $imagen_producto->ruta = str_replace('public', '/storage', $ruta_imagen);
        $imagen_producto->usuario_reg = $this->usuario->id;
        $imagen_producto->fecha_reg = now()->toDateTimeString();
        $imagen_producto->save();

        $respuesta->result = Result::SUCCESS;
        return response()->json($respuesta);
    }

    public function ajaxEliminarImagen(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoActualizar)->first();

        $respuesta = new Respuesta;
        if ($permiso === null) {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acci&oacute;n';
            return response()->json($respuesta);
        }

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $id = $request->get('id');
        $imagen_producto = ImagenProducto::find($id);
        $sRutaImagen = str_replace('/storage/', '', $imagen_producto->ruta);
        Storage::disk('public')->delete($sRutaImagen);

        $imagen_producto->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Imagen eliminada correctamente.';

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
            'nombre_en_espanol' => 'required|string|max:500',
            'nombre_en_ingles' => 'required|string|max:500',
            'beneficios_en_espanol' => 'required|string',
            'beneficios_en_ingles' => 'required|string',
            'descripcion_en_espanol' => 'required|string',
            'descripcion_en_ingles' => 'required|string',
            'modo_de_uso_en_espanol' => 'required|string',
            'modo_de_uso_en_ingles' => 'required|string',
            'categorias' => 'required',
            'lineas' => 'required'
        ]);

        $id = $request->get('id');
        $producto = Producto::find($id);

        $fecha_act = now()->toDateTimeString();

        $producto->nombre_es = $request->get('nombre_en_espanol');
        $producto->nombre_en = $request->get('nombre_en_ingles');
        $producto->beneficios_es = $request->get('beneficios_en_espanol');
        $producto->beneficios_en = $request->get('beneficios_en_ingles');
        $producto->descripcion_es = $request->get('descripcion_en_espanol');
        $producto->descripcion_en = $request->get('descripcion_en_ingles');
        $producto->modo_uso_es = $request->get('modo_de_uso_en_espanol');
        $producto->modo_uso_en = $request->get('modo_de_uso_en_ingles');
        $producto->usuario_act = $this->usuario->id;
        $producto->fecha_act = $fecha_act;
        $producto->save();

        $documentos = $request->file('documentos');
        if ($documentos) {
            $rutas_documentos = array();
            foreach ($documentos as $documento) {
                $ruta_documento = $documento->store('public/productos');
                array_push($rutas_documentos, $ruta_documento);
            }

            $nombres_documentos = $request->get('nombres_documentos');
            $documentos_producto = array();
            foreach ($nombres_documentos as $i => $nombre_documento) {
                array_push($documentos_producto, array(
                    'producto_id' => $id,
                    'nombre_descarga' => $nombre_documento,
                    'ruta_archivo' => str_replace('public', '/storage', $rutas_documentos[$i]),
                ));
            }
            DocumentoProducto::insert($documentos_producto);
        }

        ProductoCategoria::where('producto_id', $id)->delete();
        $categorias = $request->get('categorias');
        $productos_categorias = array();
        foreach ($categorias as $categoria_id) {
            array_push($productos_categorias, array(
                'producto_id' => $producto->id,
                'categoria_id' => $categoria_id,
                'usuario_reg' => $this->usuario->id,
                'fecha_reg' => $fecha_act
            ));
        }
        ProductoCategoria::insert($productos_categorias);

        ProductoLinea::where('producto_id', $id)->delete();
        $lineas = $request->get('lineas');
        $productos_lineas = array();
        foreach ($lineas as $linea_id) {
            array_push($productos_lineas, array(
                'producto_id' => $producto->id,
                'linea_id' => $linea_id,
                'usuario_reg' => $this->usuario->id,
                'fecha_reg' => $fecha_act
            ));
        }
        ProductoLinea::insert($productos_lineas);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Producto modificado correctamente.';

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

        $iProductoId = $request->get('id');

        //TODO: VERIFICAR SI EL PRODUCTO TIENE VENTAS REGISTRADAS

        $producto = Producto::find($iProductoId);

        Precio::where('producto_id', $iProductoId)->delete();
        Oferta::where('producto_id', $iProductoId)->delete();

        foreach($producto->imagenes as $img)
        {
            $url_baner = public_path().$img->ruta;
            try
            {
                unlink($url_baner);
            }catch(Exception $e)
            {}
        }

        // $sRutaImagen = str_replace('/storage/', '', $producto->ruta_imagen);
        // Storage::disk('public')->delete($sRutaImagen);

        $producto->delete();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Producto eliminado correctamente.';

        return response()->json($respuesta);
    }
}
