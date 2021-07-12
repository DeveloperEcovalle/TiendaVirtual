<?php

namespace App\Http\Controllers\Website;

use App\Blog;
use App\DetalleCarrito;
use App\Empresa;
use App\Banner;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Pagina;
use App\Precio;
use App\Producto;
use App\TelefonoEmpresa;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Tienda extends Website {

    protected $lstTraduccionesTiendaListaProductos;

    public function __construct() {
        parent::__construct();

        $this->lstTraduccionesTiendaListaProductos = [
            'en' => [
                'showing' => 'Showing',
                'of' => 'of',
                'no_results' => 'No results',
                'no_products_to_show' => 'No products to show',
                'Add to cart' => 'Add to cart',
                'Categories' => 'Categories',
                'find_your_favorite_product_here' => 'Find your favorite product here',
                'search_product' => 'Search product',
                'Related Products' => 'Related Products',
                'most_popular' => 'Most popular',
                'cheaper_first' => 'Price (lowest to highest)',
                'more_expensive_first' => 'Price (highest to lowest)',
                'description' => 'Description',
                'how_to_use' => 'How to use',
            ],
            'es' => [
                'showing' => 'Mostrando',
                'of' => 'de',
                'no_results' => 'Sin resultados',
                'no_products_to_show' => 'Sin productos para mostrar',
                'Add to cart' => 'Agregar al carrito',
                'Categories' => 'Categorías',
                'find_your_favorite_product_here' => 'Busca tu producto favorito aquí',
                'search_product' => 'Buscar producto',
                'Related Products' => 'Productos Relacionados',
                'most_popular' => 'Más populares',
                'cheaper_first' => 'Precio (de menor a mayor)',
                'more_expensive_first' => 'Precio (de mayor a menor)',
                'description' => 'Descripción',
                'how_to_use' => 'Modo de uso',
            ]
        ];
    }

    public function index(Request $request) {
        $locale = $request->session()->get('locale');

        $empresa = Empresa::with(['telefonos'])->first();
        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'lstTraduccionesTiendaListaProductos' => $this->lstTraduccionesTiendaListaProductos[$locale],
            'iPagina' => 2,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
        ];

        return view('website.tienda.lista_productos', $data);
    }

    public function ajaxListarPagina() {
        $pagina = Pagina::find(5);

        $lstBanners = Banner::where('activo', 1)->where('medio', 0)->orderBy('orden')->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['pagina' => $pagina, 'lstBanners' => $lstBanners];

        return response()->json($respuesta);
    }

    public function ajaxListarCategorias(Request $request) {
        $locale = $request->session()->get('locale');
        $orderBy = $locale === 'es' ? 'categorias_producto.nombre_es' : 'categorias_producto.nombre_en';

        $lstCategorias = DB::table('categorias_producto')
            ->join('productos_categorias', 'categorias_producto.id', '=', 'productos_categorias.categoria_id')
            ->join('productos', 'productos_categorias.producto_id', '=', 'productos.id')
            ->join('precios', 'productos.id', '=', 'precios.producto_id')
            ->whereNotNull('precios.id')
            ->select($orderBy,'categorias_producto.id', DB::raw('count(productos.id) as cantidad_productos'))
            ->groupBy('categorias_producto.id',$orderBy)
            ->orderBy($orderBy, 'asc')
            ->get();

        $data = [
            'lstCategorias' => $lstCategorias
        ];

        $respuesta = new Respuesta();
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }

    public function ajaxListarProductos(Request $request) {
        global $lstCategoriasSeleccionadas;
        $lstCategoriasSeleccionadas = $request->get('lstCategoriasSeleccionadas');

        if ($lstCategoriasSeleccionadas) {
            $sOrden = $request->get('sOrden');
            $iPaginaSeleccionada = $request->get('iPaginaSeleccionada');
            $iItemsPorPagina = $request->get('iItemsPorPagina');

            $lstProductosFiltrados = Producto::whereHas('precio_actual')->whereHas('productos_categorias', function (Builder $producto_categoria) {
                $producto_categoria->whereIn('categoria_id', $GLOBALS['lstCategoriasSeleccionadas']);
            })->with(['imagenes']);

            $iTotalProductos = $lstProductosFiltrados->count();

            switch ($sOrden) {
                case 'popular':
                    $lstProductosFiltrados = $lstProductosFiltrados->orderByRaw('sumatoria_calificaciones / cantidad_calificaciones desc');
                    break;
                case 'precio_asc':
                    $lstProductosFiltrados = $lstProductosFiltrados->orderBy(Precio::select('monto')->whereColumn('producto_id', 'productos.id')->where('actual', 1));
                    break;
                case 'precio_desc':
                    $lstProductosFiltrados = $lstProductosFiltrados->orderByDesc(Precio::select('monto')->whereColumn('producto_id', 'productos.id')->where('actual', 1));
                    break;
            }

            $offset = intval($iPaginaSeleccionada) * intval($iItemsPorPagina);
            $lstProductos = $lstProductosFiltrados->with(['precio_actual', 'oferta_vigente', 'promocion_vigente', 'imagenes'])->offset($offset)->limit($iItemsPorPagina)->get();
        } else {
            $fechaHace3Meses = now()->subMonth(3);

            $lstProductos = Producto::whereHas('precio_actual')->where('fecha_reg', '>=', $fechaHace3Meses->toDateTimeString())
                ->with(['precio_actual', 'oferta_vigente', 'promocion_vigente', 'imagenes'])->orderBy('fecha_reg', 'asc')->limit(20)->get();

            $iTotalProductos = $lstProductos->count();
        }

        $data = [
            'iTotalProductos' => $iTotalProductos,
            'lstProductos' => $lstProductos
        ];

        $respuesta = new Respuesta();
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $data;

        return response()->json($respuesta);
    }

    public function ajaxBuscarProducto(Request $request) {
        $sBuscar = $request->get('texto');

        $lstBuscar = explode(' ', $sBuscar, 3);

        $sBusqueda0 = '%' . $lstBuscar[0] . '%';
        $lstProductos = Producto::whereHas('precio_actual')
            ->where('nombre_es', 'like', $sBusqueda0);

        if (count($lstBuscar) > 1) {
            foreach ($lstBuscar as $i => $sBuscando) {
                if (strlen(trim($sBuscando)) > 2 && $i > 0) {
                    $sBusqueda = '%' . $sBuscando . '%';
                    $lstProductos = $lstProductos->orWhere('nombre_es', 'like', $sBusqueda);
                }
            }
        }

        $lstProductos = $lstProductos->with(['precio_actual', 'oferta_vigente', 'categorias', 'imagenes'])
            ->limit(8)
            ->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $lstProductos;

        return response()->json($respuesta);
    }

    public function ajaxBuscarProductoAllDatos(Request $request) {
        $sBuscar = $request->get('texto');

        $lstBuscar = explode(' ', $sBuscar, 3);

        $sBusqueda0 = '%' . $lstBuscar[0] . '%';
        $lstProductos = Producto::whereHas('precio_actual')
            ->where('nombre_es', 'like', $sBusqueda0)
            ->orWhere('beneficios_es', 'like', $sBusqueda0)
            ->orWhere('descripcion_es', 'like', $sBusqueda0);

        if (count($lstBuscar) > 1) {
            foreach ($lstBuscar as $i => $sBuscando) {
                if (strlen(trim($sBuscando)) > 2 && $i > 0) {
                    $sBusqueda = '%' . $sBuscando . '%';
                    $lstProductos = $lstProductos->orWhere('nombre_es', 'like', $sBusqueda);
                }
            }
        }

        $lstProductos = $lstProductos->with(['precio_actual', 'oferta_vigente', 'categorias', 'imagenes'])
            ->limit(8)
            ->get();

        $lstBlogs = Blog::where('titulo', 'like', $sBusqueda0)->limit(5)->get();

        $arr = array();

        foreach($lstProductos as $producto)
        {
            $producto['cabecera'] = 'producto';
            array_push($arr, $producto);
        }    
        
        if(count($lstBlogs) > 0)
        {
            foreach($lstBlogs as $blog);
            {
                $blog['cabecera'] = 'blog';
                array_push($arr, $blog);
            }
        }


        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = $arr;

        return response()->json($respuesta);
    }

    public function ajaxObtenerProductos(Request $request){
        $sBuscar = $request->get('keyword');

        $lstBuscar = explode(' ', $sBuscar, 3);

        $sBusqueda0 = '%' . $lstBuscar[0] . '%';
        $lstProductos = Producto::whereHas('precio_actual')
            ->where('nombre_es', 'like', $sBusqueda0)
            ->orWhere('beneficios_es', 'like', $sBusqueda0)
            ->orWhere('descripcion_es', 'like', $sBusqueda0);

        if (count($lstBuscar) > 1) {
            foreach ($lstBuscar as $i => $sBuscando) {
                if (strlen(trim($sBuscando)) > 2 && $i > 0) {
                    $sBusqueda = '%' . $sBuscando . '%';
                    $lstProductos = $lstProductos->orWhere('nombre_es', 'like', $sBusqueda);
                }
            }
        }

        $lstProductos = $lstProductos->with(['precio_actual', 'oferta_vigente', 'categorias', 'imagenes', 'promocion_vigente'])->get();
        $lstBlogs = Blog::where('titulo', 'like', $sBusqueda0)->limit(5)->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = array('lstProductos' => $lstProductos, 'lstBlogs' => $lstBlogs);

        return response()->json($respuesta);
    }

    public function ajaxPanelBuscar(Request $request){
        $sBuscar = $request->get('keyword');

        $locale = $request->session()->get('locale');

        $empresa = Empresa::with(['telefonos'])->first();
        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'sBuscar' => $sBuscar,
            'iPagina' => 2,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
        ];

        return view('website.tienda.productos_filtrados',$data);
    }

    public function producto(Request $request, $id) {
        $locale = $request->session()->get('locale');

        $empresa = Empresa::with(['telefonos'])->first();

        $producto = Producto::find($id);

        $data = [
            'lstLocales' => $this->lstLocales[$locale],
            'lstLocalesTiendaListaProductos' => $this->lstTraduccionesTiendaListaProductos[$locale],
            'iPagina' => 2,
            'empresa' => $empresa,
            'telefono_whatsapp' => TelefonoEmpresa::where('whatsapp', 1)->first(),
            'producto' => $producto,
        ];
        return view('website.tienda.producto', $data);
    }

    public function ajaxListarProducto(Request $request) {
        $iProductoId = $request->get('iProductoId');

        $producto = Producto::with(['precio_actual', 'oferta_vigente', 'categorias', 'documentos', 'imagenes', 'promocion_vigente'])->find($iProductoId);

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['producto' => $producto];

        return response()->json($respuesta);
    }

    public function ajaxListarProductosRelacionados(Request $request) {
        $iProductoId = $request->get('iProductoId');

        $producto = Producto::with(['categorias'])->find($iProductoId);

        global $lstCategorias;
        $lstCategorias = [];
        foreach ($producto->categorias as $categoria) {
            array_push($lstCategorias, $categoria->id);
        }

        $lstProductosRelacionados = Producto::whereHas('productos_categorias', function (Builder $producto_categoria) {
            $producto_categoria->whereIn('categoria_id', $GLOBALS['lstCategorias']);
        })->where('id', '<>', $iProductoId)->whereHas('precio_actual')->with(['imagenes', 'precio_actual', 'oferta_vigente', 'promocion_vigente'])->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = ['lstProductosRelacionados' => $lstProductosRelacionados];

        return response()->json($respuesta);
    }

    public function ajaxCargarPanel(){
        return view('website.tienda.producto-modal');
    }
}
