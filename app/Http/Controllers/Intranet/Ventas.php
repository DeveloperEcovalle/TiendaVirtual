<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\MovimientoStock;
use App\Producto;
use App\SunatFacturaBoleta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Compra;
use App\Estado;
use Illuminate\Support\Facades\DB;
use App\Empresa;
use Illuminate\Support\Facades\Mail;

class Ventas extends Intranet {

    protected $sPermisoDescargar;

    public function init() {
        parent::init();

        $this->iModuloId = 2;
        $this->iMenuId = 3;

        $this->sPermisoListar = 'GVENVENTASLISTAR';
        $this->sPermisoDescargar = 'GVENVENTASDESCARGAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        return view('intranet.gestion_ventas.ventas.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_ventas.ventas.panel_listar');
    }

    public function ajaxPanelEditar() {
        $this->init();
        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        if ($permiso === null) {
            return view('intranet.layout_right_sin_permiso');
        }

        return view('intranet.gestion_ventas.ventas.panel_editar');
    }

    /*public function ajaxListarProducto(Request $request) {
        $this->init();

        $id = $request->get('id');
        $producto = Producto::find($id);
        $producto->load('precio_actual', 'oferta_vigente');

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['producto' => $producto];

        return response()->json($respuesta);
    }*/

    public function ajaxListarAnios() {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstAnios = [];

        if ($permiso) {
            $lstAnios = DB::table('compras')
                ->select(DB::raw('year(created_at) as value'))
                ->distinct()
                ->orderBy('value', 'desc')
                ->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstAnios' => $lstAnios];

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();

        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $lstVentas = [];
        if ($permiso) {
            $lFechaDesde = $request->get('lFechaDesde');
            $lFechaHasta = $request->get('lFechaHasta');

            $sFechaDesde = Carbon::createFromTimestamp(intval($lFechaDesde) / 1000)->format('Y-m-d');
            $sFechaHasta = Carbon::createFromTimestamp(intval($lFechaHasta) / 1000)->format('Y-m-d');

            $lstVentas = Compra::whereBetween('fecha_reg', [$sFechaDesde, $sFechaHasta])
                ->with(['detalles','detalles.producto','detalles.producto.precio_actual', 'ubigeo', 'estado'])
                ->orderBy('id', 'desc')
                ->get();
             
            $lstEstados = Estado::all();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstVentas' => $lstVentas, 'lstEstados' => $lstEstados];

        return response()->json($respuesta);
    }

    public function ajaxEditarEstado(Request $request)
    {
        $respuesta = new Respuesta;

        $venta = Compra::find($request->id);
        $venta->estado_id = $request->estado_id;
        $venta->update();

        $estado = Estado::find($request->estado_id);
        $empresa = Empresa::first();

        Mail::send('website.email.confirm_pedido',compact('venta','empresa','estado'), function ($mail) use ($venta) {
            $mail->subject('ESTADO DE PEDIDO ECOVALLE');
            $mail->to($venta->email);
           $mail->from('website@ecovalle.pe','ECOVALLE');
        });

        $respuesta->result = Result::SUCCESS;
        $respuesta->mensaje = 'Estado cambiado exitosamente.';
        return response()->json($respuesta);
    }

}
