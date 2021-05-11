<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\ItemAutocompletar;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\MovimientoStock;
use App\Producto;
use Illuminate\Http\Request;

class Kardex extends Intranet {

    protected $sPermisoDescargar;

    public function init() {
        parent::init();

        $this->iModuloId = 11;
        $this->iMenuId = 15;

        $this->sPermisoListar = 'GINVKARDEXLISTAR';
        $this->sPermisoDescargar = 'GINVKARDEXDESCARGAR';
    }

    public function index($iIdInterno = 0) {
        $this->init();

        $data = ['iIdInterno' => $iIdInterno, 'iModuloId' => $this->iModuloId, 'iMenuId' => $this->iMenuId];
        return view('intranet.gestion_inventario.kardex.index', $data);
    }

    public function ajaxPanelListar() {
        return view('intranet.gestion_inventario.kardex.panel_listar');
    }

    public function ajaxAutocompletarProductos(Request $request) {
        $this->init();
        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $sBuscar = $request->get('texto');

        $lstBuscar = explode(' ', $sBuscar, 3);

        $sBusqueda0 = '%' . $lstBuscar[0] . '%';
        $data = [];

        if ($permiso) {
            $lstProductos = Producto::where('nombre_es', 'like', $sBusqueda0);

            if (count($lstBuscar) > 1) {
                foreach ($lstBuscar as $i => $sBuscando) {
                    if (strlen(trim($sBuscando)) > 2 && $i > 0) {
                        $sBusqueda = '%' . $sBuscando . '%';
                        $lstProductos = $lstProductos->orWhere('nombre_es', 'like', $sBusqueda);
                    }
                }
            }

            $lstProductos = $lstProductos->get();

            foreach ($lstProductos as $producto) {
                $item_autocompletar = new ItemAutocompletar;
                $item_autocompletar->label = $producto->nombre_es;
                $item_autocompletar->value = $producto->nombre_es;
                $item_autocompletar->entidad = $producto;

                array_push($data, $item_autocompletar);
            }
        }

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }

    public function ajaxListar(Request $request) {
        $this->init();
        $permiso = $this->perfil->permisos->where('codigo', $this->sPermisoListar)->first();

        $iProductoId = $request->get('iProductoId');

        $lstKardex = $permiso ? \App\Kardex::where('producto_id', $iProductoId)->with('usuario')->orderBy('id', 'asc')->get() : [];

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstKardex' => $lstKardex];

        return response()->json($respuesta);
    }

}
