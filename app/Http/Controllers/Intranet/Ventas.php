<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\MovimientoStock;
use App\Producto;
use App\SunatFacturaBoleta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $lstAnios = DB::table('sunat_factura_boleta')
                ->select(DB::raw('year(fecha_emision) as value'))
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

            $lstVentas = SunatFacturaBoleta::whereBetween('fecha_emision', [$sFechaDesde, $sFechaHasta])
                ->with('tipo_comprobante', 'detalles')
                ->orderBy('id', 'desc')
                ->get();
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstVentas' => $lstVentas];

        return response()->json($respuesta);
    }

}
