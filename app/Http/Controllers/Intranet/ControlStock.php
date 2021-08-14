<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Kardex;
use App\MovimientoStock;
use App\Oferta;
use App\Precio;
use App\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ControlStock extends Intranet {

    protected $sPermisoDescargar;

    public function init() {
        parent::init();

        $this->iModuloId = 11;
        $this->iMenuId = 13;

        $this->sPermisoListar = 'GINVCONTROLSTOCKLISTAR';
        $this->sPermisoInsertar = 'GINVCONTROLSTOCKINSERTAR';
        $this->sPermisoDescargar = 'GINVCONTROLSTOCKDESCARGAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        return view('intranet.gestion_inventario.control_stock.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_inventario.control_stock.panel_listar');
    }

    public function ajaxPanelEditar() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_inventario.control_stock.panel_editar');
    }

    public function ajaxListarProducto(Request $request) {
        $this->init();

        $id = $request->get('id');
        $producto = Producto::find($id);
        $producto->load('precio_actual', 'oferta_vigente');

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['producto' => $producto];

        return response()->json($respuesta);
    }


    public function ajaxListarProductos() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstProductos = $permiso ? Producto::all() : [];

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstProductos' => $lstProductos];

        return response()->json($respuesta);
    }

    public function ajaxInsertarAjuste(Request $request) {
        $this->init();
        try{
            $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoInsertar)->first();

            $respuesta = new Respuesta;
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = 'No tiene permiso para realizar esta acción.';

            if ($permiso === null) {
                return response()->json($respuesta);
            }

            $request->validate([
                'id' => 'required|numeric',
                'stock_minimo' => 'required|numeric',
                'tipo_de_ajuste' => 'required',
                'cantidad' => 'required_unless:tipo_de_ajuste,|numeric|min:1',
            ]);

            $iProductoId = $request->get('id');
            $sTipoAjuste = $request->get('tipo_de_ajuste');
            $iCantidad = $request->get('cantidad');

            $producto = Producto::find($iProductoId);

            if ($sTipoAjuste === 'S' && $producto->stock_actual < $iCantidad) {
                $respuesta->mensaje = 'Stock actual insuficiente.';
                return response()->json($respuesta);
            }

            $producto->stock_minimo = $request->get('stock_minimo');
            $producto->save();

            $fecha_reg = now();

            $movimientoStockAnterior = MovimientoStock::where('producto_id', $iProductoId)->orderBy('id', 'desc')->limit(1)->get()->get(0);

            if ($sTipoAjuste === 'S' && $producto->stock_actual >= $iCantidad) {
                $movimientoStockSalida = new MovimientoStock;
                $movimientoStockSalida->tipo = 'S';
                $movimientoStockSalida->evento = 'Salida por ajuste manual';
                $movimientoStockSalida->producto_id = $iProductoId;
                $movimientoStockSalida->cantidad = $iCantidad;
                $movimientoStockSalida->stock_anterior = $movimientoStockAnterior->stock_actual;
                $movimientoStockSalida->stock_actual = $movimientoStockAnterior->stock_actual - $iCantidad;
                $movimientoStockSalida->usuario_reg = $this->usuario->id;
                $movimientoStockSalida->fecha_reg = $fecha_reg->toDateTimeString();
                $movimientoStockSalida->save();

                $kardexEntrada = Kardex::where('producto_id', $iProductoId)->where('entrada_cantidad_restante', '>', 0)->orderBy('id', 'asc')->get()->get(0);

                $iCantidadASalirRestante = $iCantidad;
                $iStockActualProducto = $producto->stock_actual;

                while ($iCantidadASalirRestante > 0) {
                    if(!empty($kardexEntrada))
                    {
                        $salida_cantidad = $kardexEntrada->entrada_cantidad_restante > $iCantidadASalirRestante ? $iCantidadASalirRestante : $kardexEntrada->entrada_cantidad_restante;
                        $saldo_cantidad = $kardexEntrada->entrada_cantidad_restante - $salida_cantidad;
                        $acumulado_cantidad = $iStockActualProducto - $salida_cantidad;
                    }
                    else{
                        $salida_cantidad = $iCantidadASalirRestante;
                        $saldo_cantidad = $salida_cantidad;
                        $acumulado_cantidad = $iStockActualProducto - $salida_cantidad;
                    }

                    $kardexSalida = new Kardex;
                    $kardexSalida->producto_id = $iProductoId;
                    $kardexSalida->tipo = 'S';
                    $kardexSalida->descripcion = 'Salida por ajuste manual';
                    $kardexSalida->fecha_movimiento = $fecha_reg->toDateTimeString();
                    $kardexSalida->salida_cantidad = $salida_cantidad;
                    $kardexSalida->saldo_cantidad = $saldo_cantidad;
                    $kardexSalida->acumulado_cantidad = $acumulado_cantidad;
                    $kardexSalida->movimiento_stock_id = $movimientoStockSalida->id;
                    $kardexSalida->usuario_reg = $this->usuario->id;
                    $kardexSalida->fecha_reg = $fecha_reg->toDateTimeString();
                    $kardexSalida->save();

                    if(!empty($kardexEntrada))
                    {
                        $kardexEntrada->entrada_cantidad_restante -= $salida_cantidad;
                        $kardexEntrada->save();
                    }

                    $iStockActualProducto = $acumulado_cantidad;
                    $iCantidadASalirRestante -= $salida_cantidad;
                    $kardexEntrada = Kardex::where('producto_id', $iProductoId)->where('entrada_cantidad_restante', '>', 0)->orderBy('id', 'asc')->get()->get(0);
                }

                $producto->stock_actual = $iStockActualProducto;
            }

            if ($sTipoAjuste === 'E') {
                $movimientoStockEntrada = new MovimientoStock;
                $movimientoStockEntrada->tipo = 'E';
                $movimientoStockEntrada->evento = 'Entrada por ajuste manual';
                $movimientoStockEntrada->producto_id = $iProductoId;
                $movimientoStockEntrada->cantidad = $iCantidad;
                $movimientoStockEntrada->stock_anterior = $movimientoStockAnterior ? $movimientoStockAnterior->stock_actual : 0;
                $movimientoStockEntrada->stock_actual = $movimientoStockAnterior ? $movimientoStockAnterior->stock_actual + $iCantidad : $iCantidad;
                $movimientoStockEntrada->usuario_reg = $this->usuario->id;
                $movimientoStockEntrada->fecha_reg = $fecha_reg->toDateTimeString();
                $movimientoStockEntrada->save();

                $kardex = new Kardex;
                $kardex->producto_id = $iProductoId;
                $kardex->tipo = 'E';
                $kardex->descripcion = 'Entrada por ajuste manual';
                $kardex->fecha_movimiento = $fecha_reg->toDateTimeString();
                $kardex->entrada_cantidad = $iCantidad;
                $kardex->entrada_cantidad_restante = $iCantidad;
                $kardex->saldo_cantidad = $iCantidad;
                $kardex->acumulado_cantidad = $producto->stock_actual + $iCantidad;
                $kardex->movimiento_stock_id = $movimientoStockEntrada->id;
                $kardex->usuario_reg = $this->usuario->id;
                $kardex->fecha_reg = $fecha_reg->toDateTimeString();
                $kardex->save();

                $producto->stock_actual += $iCantidad;
            }

            $producto->save();

            $respuesta->result = Result::SUCCESS;
            $respuesta->mensaje = 'Ajuste manual registrado correctamente.';

            return response()->json($respuesta);
        }
        catch(Exception $e)
        {
            $respuesta->result = Result::WARNING;
            $respuesta->mensaje = $e->getMessage();
            return response()->json($respuesta);
        }
    }

}
