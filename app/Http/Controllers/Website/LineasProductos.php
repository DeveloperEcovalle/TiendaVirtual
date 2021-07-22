<?php

namespace App\Http\Controllers\Website;

use App\CategoriaBlog;
use App\Empresa;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\LineaProducto;
use App\Pagina;
use App\Producto;
use App\TelefonoEmpresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LineasProductos extends Website {

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $data = [
            'empresa' => Empresa::first(),
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'lstLocales' => $this->lstLocales[$locale],
            'iPagina' => 1,
        ];

        return view('website.nosotros.lineas_productos', $data);
    }

    public function ajaxListarLineasProductos() {
        $lstLineasProductos = LineaProducto::all();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstLineasProductos' => $lstLineasProductos];

        return response()->json($respuesta);
    }

    public function ajaxListar() {
        $pagina = Pagina::find(3);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina];

        return response()->json($respuesta);
    }

    public function ajaxListarProductosRelacionados(Request $request) {
        global $iLineaProductoId;
        $iLineaProductoId = $request->get('iLineaProductoId');

        $lstProductosRelacionados = Producto::whereHas('precio_actual')->whereHas('productos_lineas', function (Builder $producto_linea) {
            $producto_linea->where('linea_id', $GLOBALS['iLineaProductoId']);
        })->with('precio_actual', 'oferta_vigente', 'promocion_vigente', 'imagenes')->limit(8)->get();

        foreach($lstProductosRelacionados as $producto)
        {
            $producto['cantidad_calificaciones'] = $producto->cantidad_calificaciones();
            $producto['sumatoria_calificaciones'] = $producto->sumatoria_calificaciones();
        }


        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstProductos' => $lstProductosRelacionados];

        return response()->json($respuesta);
    }
}
