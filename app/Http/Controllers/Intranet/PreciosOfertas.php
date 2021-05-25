<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Oferta;
use App\Precio;
use App\Producto;
use App\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
class PreciosOfertas extends Intranet {

    protected $sPermisoListarPrecios;
    protected $sPermisoListarOfertas;

    protected $sPermisoInsertarPrecios;
    protected $sPermisoInsertarOfertas;

    protected $sPermisoEliminarPrecios;
    protected $sPermisoEliminarOfertas;

    public function init() {
        parent::init();

        $this->iModuloId = 7;
        $this->iMenuId = 10;

        $this->sPermisoListarPrecios = 'GPROPRECIOSLISTAR';
        $this->sPermisoListarOfertas = 'GPROOFERTASLISTAR';

        $this->sPermisoInsertarPrecios = 'GPROPRECIOSINSERTAR';
        $this->sPermisoInsertarOfertas = 'GPROOFERTASINSERTAR';

        $this->sPermisoEliminarPrecios = 'GPROPRECIOSELIMINAR';
        $this->sPermisoEliminarOfertas = 'GPROOFERTASELIMINAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        return view('intranet.gestion_productos.precios_ofertas.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_productos.precios_ofertas.panel_listar');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permisoInsertarPrecio = $this->perfil->permisos->where('codigo', $this->sPermisoInsertarPrecios)->first();
        $permisoInsertarOferta = $this->perfil->permisos->where('codigo', $this->sPermisoInsertarOfertas)->first();

        $permisoEliminarPrecio = $this->perfil->permisos->where('codigo', $this->sPermisoEliminarPrecios)->first();
        $permisoEliminarOferta = $this->perfil->permisos->where('codigo', $this->sPermisoEliminarOfertas)->first();

        if ($permisoInsertarPrecio === null && $permisoInsertarOferta === null && $permisoEliminarPrecio === null && $permisoEliminarOferta === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_productos.precios_ofertas.panel_editar');
    }

    public function ajaxListarProducto(Request $request) {
        $this->init();

        $id = $request->get('id');
        $producto = Producto::find($id);
        $producto->load('precio_actual', 'oferta_vigente', 'promocion_vigente');

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['producto' => $producto];

        return response()->json($respuesta);
    }

    public function ajaxListarAnios(Request $request) {
        $lstAniosPrecios = DB::table('precios')->select(DB::raw('year(fecha_reg) as value'))->distinct()->get();
        $lstAniosOfertas = DB::table('ofertas')->select(DB::raw('year(fecha_reg) as value'))->distinct()->get();
        $lstAniosPromociones = DB::table('promociones')->select(DB::raw('year(fecha_reg) as value'))->distinct()->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstAniosPrecios' => $lstAniosPrecios, 'lstAniosOfertas' => $lstAniosOfertas, 'lstAniosPromociones' => $lstAniosPromociones];

        return response()->json($respuesta);
    }

    public function ajaxListarProductos() {
        $lstProductos = Producto::with(['precio_actual', 'oferta_vigente', 'promocion_vigente'])->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstProductos' => $lstProductos];

        return response()->json($respuesta);
    }

    public function ajaxListarUltimosPrecios(Request $request) {
        $this->init();

        $id = $request->get('id');
        $producto = Producto::find($id);

        $permisoListarPrecios = $this->perfil->permisos->where('codigo', $this->sPermisoListarPrecios)->first();

        $lstUltimosPrecios = [];
        if ($permisoListarPrecios) {
            $lstUltimosPrecios = $producto->ultimos_precios;
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUltimosPrecios' => $lstUltimosPrecios];

        return response()->json($respuesta);
    }

    public function ajaxListarPrecios(Request $request) {
        $this->init();

        $permisoListarPrecios = $this->perfil->permisos->where('codigo', $this->sPermisoListarPrecios)->first();

        $lstPrecios = [];
        if ($permisoListarPrecios) {
            $id = $request->get('id');
            $fecha_desde = $request->get('fecha_desde');
            $fecha_hasta = $request->get('fecha_hasta');

            $producto = Producto::find($id);
            $lstPrecios = $producto->precios->whereBetween('fecha_reg', [$fecha_desde, $fecha_hasta]);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPrecios' => $lstPrecios];

        return response()->json($respuesta);
    }

    public function ajaxInsertarPrecio(Request $request) {
        $this->init();

        $request->validate([
            'nuevo_precio' => 'required|numeric'
        ]);

        $respuesta = new Respuesta;
        $respuesta->result = Result::WARNING;

        $fNuevoPrecio = $request->get('nuevo_precio');
        if (floatval($fNuevoPrecio) <= 0) {
            $respuesta->mensaje = 'El nuevo precio ingresado debe ser mayor a cero.';
            return response()->json($respuesta);
        }

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertarPrecios)->first();
        if ($permiso == null) {
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';
            return response()->json($respuesta);
        }

        $id = $request->get('id');
        $nuevo_precio = new Precio;
        $nuevo_precio->monto = $fNuevoPrecio;
        $nuevo_precio->eliminado = 0;
        $nuevo_precio->usuario_reg = $this->usuario->id;
        $nuevo_precio->fecha_reg = now()->toDateTimeString();

        $producto = Producto::find($id);
        Precio::where('producto_id', $id)->update(['actual' => 0]);
        $producto->precios()->save($nuevo_precio);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Precio registrado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminarPrecio(Request $request) {
        $this->init();

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $respuesta = new Respuesta;
        $respuesta->result = Result::WARNING;

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoEliminarPrecios)->first();
        if ($permiso == null) {
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';
            return response()->json($respuesta);
        }

        $id = $request->get('id');
        $precio = Precio::find($id);
        $precio->eliminado = 1;
        $precio->actual = 0;
        $precio->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Precio eliminado correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxListarUltimasOfertas(Request $request) {
        $this->init();

        $permisoListarOfertas = $this->perfil->permisos->where('codigo', $this->sPermisoListarOfertas)->first();

        $lstUltimasOfertas = [];

        if ($permisoListarOfertas) {
            $id = $request->get('id');
            $producto = Producto::find($id);
            $lstUltimasOfertas = $producto->ultimas_ofertas;
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUltimasOfertas' => $lstUltimasOfertas];

        return response()->json($respuesta);
    }

    public function ajaxListarOfertas(Request $request) {
        $this->init();

        $permisoListarOfertas = $this->perfil->permisos->where('codigo', $this->sPermisoListarOfertas)->first();

        $lstOfertas = [];

        if ($permisoListarOfertas) {
            $id = $request->get('id');
            $fecha_desde = $request->get('fecha_desde');
            $fecha_hasta = $request->get('fecha_hasta');

            $producto = Producto::find($id);
            $lstOfertas = $producto->ofertas->whereBetween('fecha_reg', [$fecha_desde, $fecha_hasta]);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstOfertas' => $lstOfertas];

        return response()->json($respuesta);
    }

    public function ajaxInsertarOferta(Request $request) {
        $this->init();

        $request->validate([
            'tipo_de_oferta' => 'required',
            'nueva_oferta' => 'required|numeric',
            'fecha_de_inicio' => 'required|date_format:Y-m-d',
            'fecha_de_vencimiento' => 'required|date_format:Y-m-d',
        ]);

        $respuesta = new Respuesta;
        $respuesta->result = Result::WARNING;

        $fNuevaOferta = $request->get('nueva_oferta');

        if (floatval($fNuevaOferta) <= 0) {
            $respuesta->mensaje = 'La nueva oferta ingresada debe ser mayor a cero.';
            return response()->json($respuesta);
        }

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertarOfertas)->first();
        if ($permiso == null) {
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';
            return response()->json($respuesta);
        }

        $id = $request->get('id');

        $fecha_inicio = $request->get('fecha_de_inicio');
        $lstOfertasExistentes = Oferta::where('producto_id', $id)->where('eliminado', 0)->whereRaw('? between fecha_inicio and fecha_vencimiento', [$fecha_inicio])->get();
        if ($lstOfertasExistentes->count()) {
            $respuesta->mensaje = 'Las fecha de inicio ingresada coincide con otra oferta ya registrada.';
            return response()->json($respuesta);
        }

        $fecha_vencimiento = $request->get('fecha_de_vencimiento');
        $lstOfertasExistentes = Oferta::where('producto_id', $id)->where('eliminado', 0)->whereRaw('? between fecha_inicio and fecha_vencimiento', [$fecha_vencimiento])->get();
        if ($lstOfertasExistentes->count()) {
            $respuesta->mensaje = 'Las fecha de vencimiento ingresada coincide con otra oferta ya registrada.';
            return response()->json($respuesta);
        }

        $tipo_oferta = $request->get('tipo_de_oferta');

        $nueva_oferta = new Oferta();
        $nueva_oferta->porcentaje = $tipo_oferta === 'Porcentaje' ? $fNuevaOferta : null;
        $nueva_oferta->monto = $tipo_oferta === 'Monto' ? $fNuevaOferta : null;
        $nueva_oferta->fecha_inicio = $fecha_inicio;
        $nueva_oferta->fecha_vencimiento = $fecha_vencimiento;
        $nueva_oferta->eliminado = 0;
        $nueva_oferta->usuario_reg = $this->usuario->id;
        $nueva_oferta->fecha_reg = now()->toDateTimeString();

        $producto = Producto::find($id);
        $producto->ofertas()->save($nueva_oferta);

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Oferta registrada correctamente.';

        return response()->json($respuesta);
    }

    public function ajaxEliminarOferta(Request $request) {
        $this->init();

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $respuesta = new Respuesta;
        $respuesta->result = Result::WARNING;

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoEliminarOfertas)->first();
        if ($permiso == null) {
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';
            return response()->json($respuesta);
        }

        $id = $request->get('id');
        $oferta = Oferta::find($id);
        $oferta->eliminado = 1;
        $oferta->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Oferta eliminada correctamente.';

        return response()->json($respuesta);
    }

    /*----------------------------------------------------*/
    public function ajaxListarUltimasPromociones(Request $request) {
        $this->init();

        $permisoListarPromociones = $this->perfil->permisos->where('codigo', $this->sPermisoListarOfertas)->first();

        $lstUltimasPromociones = [];

        if ($permisoListarPromociones) {
            $id = $request->get('id');
            $producto = Producto::find($id);
            $lstUltimasPromociones = $producto->ultimas_promociones;
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstUltimasPromociones' => $lstUltimasPromociones];

        return response()->json($respuesta);
    }

    public function ajaxListarPromociones(Request $request) {
        $this->init();

        $permisoListarPromociones = $this->perfil->permisos->where('codigo', $this->sPermisoListarOfertas)->first();

        $lstPromociones = [];

        if ($permisoListarPromociones) {
            $id = $request->get('id');
            $fecha_desde = $request->get('fecha_desde');
            $fecha_hasta = $request->get('fecha_hasta');

            $producto = Producto::find($id);
            $lstPromociones = $producto->promociones->whereBetween('fecha_reg', [$fecha_desde, $fecha_hasta]);
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstPromociones' => $lstPromociones];

        return response()->json($respuesta);
    }

    public function ajaxInsertarPromocion(Request $request) {
        $this->init();
        try
        {
            $request->validate([
                'tipo_de_promocion' => 'required',
                'descripcion' => 'required',
                'nueva_promocion' => 'required|numeric',
                'fecha_de_inicio' => 'required|date_format:Y-m-d',
                'fecha_de_vencimiento' => 'required|date_format:Y-m-d',
            ]);
    
            $respuesta = new Respuesta;
            $respuesta->result = Result::WARNING;
    
            $fNuevaPromocion = $request->get('nueva_promocion');
            $fDescripcionPromocion = $request->get('descripcion');
            $fMinPromocion = $request->get('min');
            $fMaxPromocion = $request->get('max');
    
            if (floatval($fNuevaPromocion) <= 0) {
                $respuesta->mensaje = 'La nueva promocion ingresada debe ser mayor a cero.';
                return response()->json($respuesta);
            }
    
            $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertarOfertas)->first();
            if ($permiso == null) {
                $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';
                return response()->json($respuesta);
            }
    
            $id = $request->get('id');
    
            $fecha_inicio = $request->get('fecha_de_inicio');
            $lstPromocionesExistentes = Promocion::where('producto_id', $id)->where('eliminado', 0)->whereRaw('? between fecha_inicio and fecha_vencimiento', [$fecha_inicio])->get();
            if ($lstPromocionesExistentes->count()) {
                $respuesta->mensaje = 'Las fecha de inicio ingresada coincide con otra promocion ya registrada.';
                return response()->json($respuesta);
            }
    
            $fecha_vencimiento = $request->get('fecha_de_vencimiento');
            $lstPromocionesExistentes = Promocion::where('producto_id', $id)->where('eliminado', 0)->whereRaw('? between fecha_inicio and fecha_vencimiento', [$fecha_vencimiento])->get();
            if ($lstPromocionesExistentes->count()) {
                $respuesta->mensaje = 'Las fecha de vencimiento ingresada coincide con otra promocion ya registrada.';
                return response()->json($respuesta);
            }
    
            $tipo_promocion = $request->get('tipo_de_promocion');
    
            $nueva_promocion = new Promocion();
            $nueva_promocion->porcentaje = $tipo_promocion === 'Porcentaje' ? $fNuevaPromocion : null;
            $nueva_promocion->monto = $tipo_promocion === 'Monto' ? $fNuevaPromocion : null;
            $nueva_promocion->fecha_inicio = $fecha_inicio;
            $nueva_promocion->descripcion = $fDescripcionPromocion;
            $nueva_promocion->min = $fMinPromocion;
            $nueva_promocion->max = $fMaxPromocion;
            $nueva_promocion->fecha_vencimiento = $fecha_vencimiento;
            $nueva_promocion->eliminado = 0;
            $nueva_promocion->usuario_reg = $this->usuario->id;
            $nueva_promocion->fecha_reg = now()->toDateTimeString();
    
            $producto = Producto::find($id);
            $producto->promociones()->save($nueva_promocion);
    
            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Promoción registrada correctamente.';
    
            return response()->json($respuesta);
        }catch(Exception $e)
        {
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
        }
    }

    public function ajaxEliminarPromocion(Request $request) {
        $this->init();

        $request->validate([
            'id' => 'required|numeric'
        ]);

        $respuesta = new Respuesta;
        $respuesta->result = Result::WARNING;

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoEliminarOfertas)->first();
        if ($permiso == null) {
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';
            return response()->json($respuesta);
        }

        $id = $request->get('id');
        $promocion = Promocion::find($id);
        $promocion->eliminado = 1;
        $promocion->save();

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Promoción eliminada correctamente.';

        return response()->json($respuesta);
    }
}
