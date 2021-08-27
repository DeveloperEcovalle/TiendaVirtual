<?php

use App\Agencia;
use App\Cliente;
use App\Compra;
use App\Empresa;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\UsuarioAutenticado;
use App\Http\Middleware\ClienteAutenticado;
use App\Http\Middleware\AutenticarUsuario;
use App\Http\Middleware\Locale;
use App\Persona;
use App\Producto;
use App\Venta;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Estado;
use Barryvdh\DomPDF\Facade as PDF;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware([Locale::class])->group(function () {
    Route::namespace('Website')->group(function () {
        Route::post('/language/{language}', 'Website@language')->withoutMiddleware([Locale::class]);
        Route::get('/', 'Tienda@index');
        Route::get('/index', 'Inicio@index');

        Route::prefix('/ajax')->group(function () {
            Route::post('/locale', 'Website@ajaxLocale');
            Route::post('/enviarCorreoContactoContigo', 'Website@ajaxEnviarCorreoContactoContigo');
            Route::post('/data', 'Inicio@ajaxData');

            Route::post('/listarCarrito', 'Website@ajaxListarCarrito');
            Route::post('/agregarAlCarrito', 'Website@ajaxAgregarAlCarrito');
            Route::post('/eliminarDelCarrito', 'Website@ajaxEliminarDelCarrito');
            Route::post('/disminuirCantidadProductoCarrito', 'Website@ajaxDisminuirCantidadProductoCarrito');
            Route::post('/aumentarCantidadProductoCarrito', 'Website@ajaxAumentarCantidadProductoCarrito');
            Route::post('/aumentarCantidadProductoCarritoCantidad', 'Website@ajaxAumentarCantidadProductoCarritoCantidad');
            Route::post('/findproducto', 'Website@findproducto');
            Route::get('/publicidad', 'Website@ajaxPublicidad');
        });

        Route::prefix('/nosotros')->group(function () {
            Route::permanentRedirect('/', '/nosotros/quienes-somos');

            Route::prefix('/quienes-somos')->group(function () {
                Route::get('/', 'QuienesSomos@index');

                Route::prefix('/ajax')->group(function () {
                    Route::post('/listar', 'QuienesSomos@ajaxListar');
                });
            });

            Route::prefix('/certificaciones')->group(function () {
                Route::get('/', 'Certificaciones@index');

                Route::prefix('/ajax')->group(function () {
                    Route::post('/listar', 'Certificaciones@ajaxListar');
                });
            });

            Route::prefix('/lineas-productos')->group(function () {
                Route::get('/', 'LineasProductos@index');

                Route::prefix('/ajax')->group(function () {
                    Route::post('/listarLineasProductos', 'LineasProductos@ajaxListarLineasProductos');
                    Route::post('/listar', 'LineasProductos@ajaxListar');
                    Route::post('/listarProductosRelacionados', 'LineasProductos@ajaxListarProductosRelacionados');
                });
            });
        });

        Route::prefix('/guia-compras')->group(function () {
            Route::get('/', 'GuiaCompras@index');

            Route::prefix('/ajax')->group(function () {
                Route::post('/listar', 'GuiaCompras@ajaxListar');
            });
        });

        Route::prefix('/servicios')->group(function () {
            Route::get('/', 'Servicios@index');

            Route::prefix('/ajax')->group(function () {
                Route::post('/listar', 'Servicios@ajaxListar');
                Route::post('/enviar', 'Servicios@ajaxEnviar');
            });
        });

        Route::prefix('/se-ecovalle')->group(function () {
            Route::permanentRedirect('/', '/se-ecovalle/socios');

            Route::prefix('/socios')->group(function () {
                Route::get('/', 'Socios@index');

                Route::prefix('/ajax')->group(function () {
                    Route::post('/listar', 'Socios@ajaxListar');
                    Route::post('/enviar', 'Socios@ajaxEnviar');
                });
            });

            Route::prefix('/recursos-humanos')->group(function () {
                Route::get('/', 'RecursosHumanos@index');

                Route::prefix('/ajax')->group(function () {
                    Route::post('/listar', 'RecursosHumanos@ajaxListar');
                    Route::post('/enviar', 'RecursosHumanos@ajaxEnviar');
                });
            });
        });

        Route::prefix('/tienda')->group(function () {
            Route::get('/', 'Tienda@index');

            Route::prefix('/ajax')->group(function () {
                Route::post('/listarPagina', 'Tienda@ajaxListarPagina');
                Route::post('/listarCategorias', 'Tienda@ajaxListarCategorias');
                Route::post('/listarProductos', 'Tienda@ajaxListarProductos');
                Route::post('/buscarProducto', 'Tienda@ajaxBuscarProducto');
                Route::post('/buscarProductoAllDatos', 'Tienda@ajaxBuscarProductoAllDatos');
                Route::post('/panelBuscar', 'Tienda@ajaxPanelBuscar')->name('tienda.buscarProducto');
                Route::post('/obtenerProductos', 'Tienda@ajaxObtenerProductos');
            });

            Route::prefix('/producto')->group(function () {
                Route::get('/{id}', 'Tienda@producto');

                Route::prefix('/ajax')->group(function () {
                    Route::post('/listarProducto', 'Tienda@ajaxListarProducto');
                    Route::get('/cargarPanel', 'Tienda@ajaxCargarPanel');
                    Route::post('/listarProductosRelacionados', 'Tienda@ajaxListarProductosRelacionados');
                });
            });
        });

        Route::prefix('/carrito-compras')->group(function () {
            Route::get('/', 'CarritoCompras@index');
        });

        Route::prefix('/facturacion-envio')->group(function () {
            Route::get('/', 'FacturacionEnvio@index');

            Route::prefix('/ajax')->group(function () {
                Route::get('/listarPreciosEnvio', 'FacturacionEnvio@ajaxListarPreciosEnvio');
                Route::post('/listarDatosFacturacion', 'FacturacionEnvio@ajaxListarDatosFacturacion');
                Route::post('/consultaApi', 'FacturacionEnvio@ajaxConsultaApi');
            });
        });

        Route::prefix('/pago-envio')->group(function () {
            Route::get('/', 'PagoEnvio@index');

            Route::prefix('/ajax')->group(function () {
                Route::get('/listarPreciosEnvio', 'PagoEnvio@ajaxListarPreciosEnvio');
                Route::post('/listarDatosPago', 'PagoEnvio@ajaxListarDatosPago');
                Route::post('/crearCargo', 'PagoEnvio@ajaxCrearCargo');
                Route::post('/crearVenta', 'PagoEnvio@ajaxCrearVenta');
            });
        });

        Route::prefix('/blog')->group(function () {
            Route::get('/', 'Blog@index');

            Route::prefix('/ajax')->group(function () {
                Route::post('/listarCategorias', 'Blog@ajaxListarCategorias');
                Route::post('/listarPublicaciones', 'Blog@ajaxListarPublicaciones');

                Route::post('/listarPublicacion', 'Blog@ajaxListarPublicacion');
                Route::post('/listarUltimasPublicaciones', 'Blog@ajaxListarUltimasPublicaciones');
            });
        });

        Route::prefix('/mi-cuenta')->group(function () {
            Route::get('/', 'MiCuenta@index');

            Route::prefix('/ajax')->group(function () {
                Route::get('/panelDesk', 'MiCuenta@ajaxListarPanelDesk');

                Route::get('/panelAccount', 'MiCuenta@ajaxListarPanelAccount');
                Route::post('/actualizarAccount', 'MiCuenta@ajaxActualizarAccount');

                Route::get('/panelAddress', 'MiCuenta@ajaxListarPanelAddress');
                Route::post('/actualizarAddress', 'MiCuenta@ajaxActualizarAddress');

                Route::get('/panelOrders', 'MiCuenta@ajaxListarPanelOrders');
                Route::get('/listarOrders', 'MiCuenta@ajaxListarOrders');
                Route::get('/download/{codigo}', 'MiCuenta@ajaxDownload');

                Route::get('/panelShow', 'MiCuenta@ajaxListarPanelShow');
            });
        });

        Route::prefix('/olvide-mi-contrasena')->group(function(){
            Route::get('/', 'RecuperarContra@index');
            Route::prefix('/ajax')->group(function () {
                Route::post('/enviar', 'RecuperarContra@ajaxEnviar');
            });
        });

        Route::prefix('/contactanos')->group(function () {
            Route::get('/', 'Contactanos@index');
            Route::post('/ajax/enviar', 'Contactanos@ajaxEnviar');
        });

        Route::prefix('/terminos-condiciones')->group(function () {
            Route::get('/', 'TerminosCondiciones@index');

            Route::prefix('/ajax')->group(function () {
                Route::post('/listar', 'TerminosCondiciones@ajaxListar');
            });
        });

        Route::prefix('/libro-reclamaciones')->group(function () {
            Route::get('/', 'LibroReclamaciones@index');
            
            Route::prefix('/ajax')->group(function () {
                Route::post('/listar', 'LibroReclamaciones@ajaxListar');
                Route::post('/enviar', 'LibroReclamaciones@ajaxEnviar');
            });
        });

        Route::prefix('/politica-privacidad')->group(function () {
            Route::get('/', 'PoliticaPrivacidad@index');

            Route::prefix('/ajax')->group(function () {
                Route::post('/listar', 'PoliticaPrivacidad@ajaxListar');
            });
        });

        Route::prefix('/iniciar-sesion')->group(function () {
            //Route::get('/', 'IniciarSesion@index')->middleware([ClienteAutenticado::class]);
            Route::post('/ajax/ingresar', 'IniciarSesion@ajaxIngresar');
            Route::post('/ajax/salir', 'IniciarSesion@ajaxSalir');
        });

        Route::prefix('/registro')->group(function () {
            Route::get('/', 'Registro@index')->middleware([ClienteAutenticado::class]);
            Route::post('/ajax/registrar', 'Registro@ajaxRegistrar');
            Route::get('/ajax/listarDatos', 'Registro@ajaxListarDatos');
            Route::post('/anclarSession', 'Registro@anclarSession');
        });
    });
});

Route::namespace('Intranet')->group(function () {
    Route::prefix('/intranet')->group(function () {
        Route::permanentRedirect('/', '/intranet/login');

        Route::prefix('/login')->group(function () {
            Route::get('/', 'Login@index')->middleware([UsuarioAutenticado::class]);
            Route::post('/ajax/ingresar', 'Login@ajaxIngresar');
        });

        Route::get('/salir', 'Login@salir');

        Route::post('/ajax/listar-menus', 'Intranet@ajaxListarMenus');

        Route::middleware([AutenticarUsuario::class])->group(function () {
            Route::prefix('/app')->group(function () {
                Route::get('/panel-control', 'PanelControl@index');

                Route::prefix('/gestion-ventas')->group(function () {
                    Route::prefix('/ventas')->group(function () {
                        Route::get('/', 'Ventas@index');
                        Route::get('/{iIdInterno?}/editar', 'Ventas@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Ventas@ajaxPanelListar');
                            Route::get('/panelEditar', 'Ventas@ajaxPanelEditar');

                            Route::post('/listarAnios', 'Ventas@ajaxListarAnios');
                            Route::post('/listar', 'Ventas@ajaxListar');
                            Route::post('/editarEstado', 'Ventas@ajaxEditarEstado');
                            /*Route::post('/listarProducto', 'Ventas@ajaxListarProducto');
                            Route::post('/insertarAjuste', 'Ventas@ajaxInsertarAjuste');*/
                        });
                    });
                });

                Route::prefix('/libro-reclamaciones')->group(function() {
                    Route::prefix('/libro')->group(function() {
                        Route::get('/','GestionLibro@index');
                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'GestionLibro@ajaxListar');
                            Route::get('/download/{id}', 'GestionLibro@ajaxDownload');
                        });
                    });
                });

                Route::prefix('/gestion-productos')->group(function () {

                    Route::prefix('/categorias')->group(function () {
                        Route::get('/', 'CategoriasProductos@index');
                        Route::get('/nuevo', 'CategoriasProductos@index');
                        Route::get('/{iIdInterno?}/editar', 'CategoriasProductos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'CategoriasProductos@ajaxPanelListar');
                            Route::get('/panelNuevo', 'CategoriasProductos@ajaxPanelNuevo');
                            Route::get('/panelEditar', 'CategoriasProductos@ajaxPanelEditar');

                            Route::post('/listar', 'CategoriasProductos@ajaxListar');
                            Route::post('/insertar', 'CategoriasProductos@ajaxInsertar');
                            Route::post('/actualizar', 'CategoriasProductos@ajaxActualizar');
                            Route::post('/eliminar', 'CategoriasProductos@ajaxEliminar');
                        });
                    });

                    Route::prefix('/lineas')->group(function () {
                        Route::get('/', 'LineasProductos@index');
                        Route::get('/nuevo', 'LineasProductos@index');
                        Route::get('/{iIdInterno?}/editar', 'LineasProductos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'LineasProductos@ajaxPanelListar');
                            Route::get('/panelNuevo', 'LineasProductos@ajaxPanelNuevo');
                            Route::get('/panelEditar', 'LineasProductos@ajaxPanelEditar');

                            Route::post('/listar', 'LineasProductos@ajaxListar');
                            Route::post('/insertar', 'LineasProductos@ajaxInsertar');
                            Route::post('/actualizar', 'LineasProductos@ajaxActualizar');
                            Route::post('/eliminar', 'LineasProductos@ajaxEliminar');
                            Route::post('/eliminarImagen', 'LineasProductos@ajaxEliminarImagen');
                        });
                    });

                    Route::prefix('/productos')->group(function () {
                        Route::get('/', 'Productos@index');
                        Route::get('/nuevo', 'Productos@index');
                        Route::get('/{iIdInterno?}/editar', 'Productos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Productos@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Productos@ajaxPanelNuevo');
                            Route::post('/nuevo/listarData', 'Productos@ajaxNuevoListarData');
                            Route::get('/panelEditar', 'Productos@ajaxPanelEditar');
                            Route::post('/editar/listarData', 'Productos@ajaxEditarListarData');
                            Route::post('/editar/autocompletarProductos', 'Productos@ajaxEditarAutocompletarProductos');
                            Route::post('/editar/insertarSubproducto', 'Productos@ajaxEditarInsertarSubproducto');

                            Route::post('/listar', 'Productos@ajaxListar');
                            Route::post('/insertar', 'Productos@ajaxInsertar');
                            Route::post('/eliminarDocumento', 'Productos@ajaxEliminarDocumento');
                            Route::post('/listarImagenes', 'Productos@ajaxListarImagenes');
                            Route::post('/insertarImagen', 'Productos@ajaxInsertarImagen');
                            Route::post('/eliminarImagen', 'Productos@ajaxEliminarImagen');
                            Route::post('/actualizar', 'Productos@ajaxActualizar');
                            Route::post('/eliminar', 'Productos@ajaxEliminar');
                        });
                    });


                    Route::prefix('/precios-ofertas')->group(function () {
                        Route::get('/', 'PreciosOfertas@index');
                        Route::get('/{iIdInterno?}/editar', 'PreciosOfertas@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'PreciosOfertas@ajaxPanelListar');
                            Route::get('/panelEditar', 'PreciosOfertas@ajaxPanelEditar');

                            Route::post('/listarProducto', 'PreciosOfertas@ajaxListarProducto');
                            Route::post('/listarAnios', 'PreciosOfertas@ajaxListarAnios');
                            Route::post('/listarProductos', 'PreciosOfertas@ajaxListarProductos');

                            Route::post('/listarUltimosPrecios', 'PreciosOfertas@ajaxListarUltimosPrecios');
                            Route::post('/listarPrecios', 'PreciosOfertas@ajaxListarPrecios');
                            Route::post('/insertarPrecio', 'PreciosOfertas@ajaxInsertarPrecio');
                            Route::post('/eliminarPrecio', 'PreciosOfertas@ajaxEliminarPrecio');

                            Route::post('/listarUltimasOfertas', 'PreciosOfertas@ajaxListarUltimasOfertas');
                            Route::post('/listarOfertas', 'PreciosOfertas@ajaxListarOfertas');
                            Route::post('/insertarOferta', 'PreciosOfertas@ajaxInsertarOferta');
                            Route::post('/eliminarOferta', 'PreciosOfertas@ajaxEliminarOferta');

                            Route::post('/listarUltimasPromociones', 'PreciosOfertas@ajaxListarUltimasPromociones');
                            Route::post('/listarPromociones', 'PreciosOfertas@ajaxListarPromociones');
                            Route::post('/insertarPromocion', 'PreciosOfertas@ajaxInsertarPromocion');
                            Route::post('/eliminarPromocion', 'PreciosOfertas@ajaxEliminarPromocion');
                        });
                    });
                });

                Route::prefix('/gestion-inventario')->group(function () {
                    Route::prefix('/control-stock')->group(function () {
                        Route::get('/', 'ControlStock@index');
                        Route::get('/{iIdInterno?}/editar', 'ControlStock@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'ControlStock@ajaxPanelListar');
                            Route::get('/panelEditar', 'ControlStock@ajaxPanelEditar');

                            Route::post('/listarProductos', 'ControlStock@ajaxListarProductos');

                            Route::post('/listarProducto', 'ControlStock@ajaxListarProducto');
                            Route::post('/insertarAjuste', 'ControlStock@ajaxInsertarAjuste');
                        });
                    });

                    Route::prefix('/movimientos-stock')->group(function () {
                        Route::get('/', 'MovimientosStock@index');
                        Route::get('/{iIdInterno?}/editar', 'MovimientosStock@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'MovimientosStock@ajaxPanelListar');
                            Route::get('/panelEditar', 'MovimientosStock@ajaxPanelEditar');

                            Route::post('/listarAnios', 'MovimientosStock@ajaxListarAnios');
                            Route::post('/listar', 'MovimientosStock@ajaxListar');
                        });
                    });

                    Route::prefix('/kardex')->group(function () {
                        Route::get('/', 'Kardex@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/autocompletarProductos', 'Kardex@ajaxAutocompletarProductos');
                            Route::post('/listar', 'Kardex@ajaxListar');
                        });
                    });

                    Route::prefix('/precios-envio')->group(function () {
                        Route::get('/', 'PreciosEnvio@index');
                        Route::get('/nuevo', 'PreciosEnvio@index');
                        Route::get('/{iIdInterno?}/editar', 'PreciosEnvio@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'PreciosEnvio@ajaxPanelListar');
                            Route::get('/panelNuevo', 'PreciosEnvio@ajaxPanelNuevo');
                            Route::get('/panelEditar', 'PreciosEnvio@ajaxPanelEditar');

                            Route::post('/listar', 'PreciosEnvio@ajaxListar');
                            Route::post('/listarUbigeo', 'PreciosEnvio@ajaxListarUbigeo');
                            Route::post('/insertar', 'PreciosEnvio@ajaxInsertar');
                            Route::post('/actualizar', 'PreciosEnvio@ajaxActualizar');
                            Route::post('/eliminar', 'PreciosEnvio@ajaxEliminar');
                        });
                    });
                });

                Route::prefix('/personas')->group(function () {
                    Route::prefix('/clientes')->group(function () {
                        Route::get('/', 'Clientes@index');
                        Route::get('/nuevo', 'Clientes@index');
                        Route::get('/{iIdInterno?}/editar', 'Clientes@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Clientes@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Clientes@ajaxPanelNuevo');
                            Route::post('/nuevo/listarData', 'Clientes@ajaxNuevoListarData');
                            Route::post('/nuevo/consultarDni', 'Clientes@ajaxNuevoConsultarDni');
                            Route::post('/nuevo/consultarRuc', 'Clientes@ajaxNuevoConsultarRuc');
                            Route::get('/panelEditar', 'Clientes@ajaxPanelEditar');
                            Route::post('/editar/consultarDni', 'Clientes@ajaxEditarConsultarDni');
                            Route::post('/editar/consultarRuc', 'Clientes@ajaxEditarConsultarRuc');
                            Route::post('/editar/listarData', 'Clientes@ajaxEditarListarData');

                            Route::post('/listar', 'Clientes@ajaxListar');
                            Route::post('/insertar', 'Clientes@ajaxInsertar');
                            Route::post('/actualizar', 'Clientes@ajaxActualizar');
                            Route::post('/eliminar', 'Clientes@ajaxEliminar');
                        });
                    });

                    Route::prefix('/proveedores')->group(function () {
                        Route::get('/', 'Proveedores@index');
                        Route::get('/nuevo', 'Proveedores@index');
                        Route::get('/{iIdInterno?}/editar', 'Proveedores@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Proveedores@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Proveedores@ajaxPanelNuevo');
                            Route::post('/nuevo/listarData', 'Proveedores@ajaxNuevoListarData');
                            Route::post('/nuevo/consultarRuc', 'Proveedores@ajaxNuevoConsultarRuc');
                            Route::get('/panelEditar', 'Proveedores@ajaxPanelEditar');
                            Route::post('/editar/listarData', 'Proveedores@ajaxEditarListarData');

                            Route::post('/listar', 'Proveedores@ajaxListar');
                            Route::post('/insertar', 'Proveedores@ajaxInsertar');
                            Route::post('/actualizar', 'Proveedores@ajaxActualizar');
                            Route::post('/eliminar', 'Proveedores@ajaxEliminar');
                        });
                    });
                });

                Route::prefix('/pagina-web')->group(function () {
                    Route::prefix('/galeria-imagenes')->group(function () {
                        Route::get('/', 'GaleriaImagenes@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listarImagenes', 'GaleriaImagenes@ajaxListarImagenes');
                            Route::post('/insertarImagen', 'GaleriaImagenes@ajaxInsertarImagen');
                            Route::post('/eliminarImagen', 'GaleriaImagenes@ajaxEliminarImagen');
                        });
                    });

                    Route::prefix('/banners')->group(function () {
                        Route::get('/', 'Banners@index');
                        Route::get('/nuevo', 'Banners@index');
                        Route::get('/{iIdInterno?}/editar', 'Banners@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Banners@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Banners@ajaxPanelNuevo');
                            Route::get('/panelEditar', 'Banners@ajaxPanelEditar');

                            Route::post('/listar', 'Banners@ajaxListar');
                            Route::post('/insertar', 'Banners@ajaxInsertar');
                            Route::post('/actualizar', 'Banners@ajaxActualizar');
                            Route::post('/eliminar', 'Banners@ajaxEliminar');
                        });
                    });

                    Route::prefix('/inicio')->group(function () {
                        Route::get('/', 'Inicio@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Inicio@ajaxListar');
                            Route::post('/actualizarContenidoEspanol', 'Inicio@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'Inicio@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/quienes-somos')->group(function () {
                        Route::get('/', 'QuienesSomos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'QuienesSomos@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'QuienesSomos@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'QuienesSomos@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'QuienesSomos@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/lineas-productos')->group(function () {
                        Route::get('/', 'WebLineasProductos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'WebLineasProductos@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'WebLineasProductos@ajaxActualizarImagenPortada');
                            Route::post('/actualizarBaner', 'WebLineasProductos@ajaxActualizarBaner');
                            Route::post('/actualizarContenidoEspanol', 'WebLineasProductos@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'WebLineasProductos@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/certificaciones')->group(function () {
                        Route::get('/', 'Certificaciones@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Certificaciones@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'Certificaciones@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'Certificaciones@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'Certificaciones@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/guia-compras')->group(function () {
                        Route::get('/', 'GuiaCompras@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'GuiaCompras@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'GuiaCompras@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'GuiaCompras@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'GuiaCompras@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/tienda')->group(function () {
                        Route::get('/', 'Tienda@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Tienda@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'Tienda@ajaxActualizarImagenPortada');
                            Route::post('/actualizarBaner', 'Tienda@ajaxActualizarBaner');
                            Route::post('/actualizarContenidoEspanol', 'Tienda@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'Tienda@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/servicios')->group(function () {
                        Route::get('/', 'Servicios@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Servicios@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'Servicios@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'Servicios@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'Servicios@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/socios')->group(function () {
                        Route::get('/', 'Socios@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Socios@ajaxListar');
                            Route::get('/ajaxPanelEditarBeneficio', 'Socios@ajaxPanelEditarBeneficio');
                            Route::get('/ajaxPanelCrearBeneficio', 'Socios@ajaxPanelCrearBeneficio');
                            Route::post('/ajaxActualizarBeneficio', 'Socios@ajaxActualizarBeneficio');
                            Route::post('/ajaxEliminarBeneficio', 'Socios@ajaxEliminarBeneficio');
                            Route::post('/ajaxCrearBeneficio', 'Socios@ajaxCrearBeneficio');
                            Route::post('/actualizarImagenPortada', 'Socios@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'Socios@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'Socios@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/recursos-humanos')->group(function () {
                        Route::get('/', 'RecursosHumanos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'RecursosHumanos@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'RecursosHumanos@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'RecursosHumanos@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'RecursosHumanos@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/blog')->group(function () {
                        Route::get('/', 'Blogs@index');
                        Route::get('/nuevo', 'Blogs@index');
                        Route::get('/{iIdInterno?}/editar', 'Blogs@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Blogs@ajaxPanelListar');

                            Route::get('/panelNuevo', 'Blogs@ajaxPanelNuevo');
                            Route::post('/nuevo/listarCategorias', 'Blogs@ajaxNuevoListarCategorias');
                            Route::post('/nuevo/insertarCategoria', 'Blogs@ajaxNuevoInsertarCategoria');
                            Route::post('/nuevo/eliminarCategoria', 'Blogs@ajaxNuevoEliminarCategoria');

                            Route::get('/panelEditar', 'Blogs@ajaxPanelEditar');
                            Route::post('/editar/listarCategorias', 'Blogs@ajaxEditarListarCategorias');
                            Route::post('/editar/insertarCategoria', 'Blogs@ajaxEditarInsertarCategoria');
                            Route::post('/editar/eliminarCategoria', 'Blogs@ajaxEditarEliminarCategoria');

                            Route::post('/listarAnios', 'Blogs@ajaxListarAnios');
                            Route::post('/listar', 'Blogs@ajaxListar');
                            Route::post('/insertar', 'Blogs@ajaxInsertar');
                            Route::post('/actualizar', 'Blogs@ajaxActualizar');
                            Route::post('/eliminar', 'Blogs@ajaxEliminar');

                            Route::post('/actualizarImagenPortada', 'Blogs@ajaxActualizarImagenPortada');
                            Route::post('/actualizarBaner', 'Blogs@ajaxActualizarBaner');
                        });
                    });

                    Route::prefix('/contactanos')->group(function () {
                        Route::get('/', 'Contactanos@index');
                        Route::get('/nuevo', 'Contactanos@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Contactanos@ajaxListar');
                            Route::post('/actualizarImagenContactanos', 'Contactanos@ajaxActualizarImagenContactanos');
                            Route::post('/actualizarDireccion', 'Contactanos@ajaxActualizarDireccion');
                            Route::post('/actualizarRedesSociales', 'Contactanos@ajaxActualizarRedesSociales');
                            Route::post('/actualizarCorreo', 'Contactanos@ajaxActualizarCorreo');
                            Route::post('/actualizarEnlaceMapa', 'Contactanos@ajaxActualizarEnlaceMapa');
                            Route::post('/insertarTelefono', 'Contactanos@ajaxInsertarTelefono');
                            Route::post('/eliminarTelefono', 'Contactanos@ajaxEliminarTelefono');
                        });

                    });

                    Route::prefix('/terminos-condiciones')->group(function () {
                        Route::get('/', 'TerminosCondiciones@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'TerminosCondiciones@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'TerminosCondiciones@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'TerminosCondiciones@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'TerminosCondiciones@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/politica-privacidad')->group(function () {
                        Route::get('/', 'PoliticaPrivacidad@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'PoliticaPrivacidad@ajaxListar');
                            Route::post('/actualizarImagenPortada', 'PoliticaPrivacidad@ajaxActualizarImagenPortada');
                            Route::post('/actualizarContenidoEspanol', 'PoliticaPrivacidad@ajaxActualizarContenidoEspanol');
                            Route::post('/actualizarContenidoIngles', 'PoliticaPrivacidad@ajaxActualizarContenidoIngles');
                        });
                    });

                    Route::prefix('/libro-reclamaciones')->group(function () {
                        Route::get('/','LibroReclamaciones@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'LibroReclamaciones@ajaxListar');
                            Route::post('/actualizarImagenLibro', 'LibroReclamaciones@ajaxActualizarImagenLibro');
                            Route::post('/actualizarRuc', 'LibroReclamaciones@ajaxActualizarRuc');
                            Route::post('/actualizarRazon', 'LibroReclamaciones@ajaxActualizarRazon');
                            Route::post('/actualizarMensaje', 'LibroReclamaciones@ajaxActualizarMensaje');
                        });
                    });

                    Route::prefix('/carrito-compras')->group(function () {
                        Route::get('/','GestionCarrito@index');
                        Route::get('/nuevo', 'GestionCarrito@index');
                        Route::get('/{iIdInterno?}/editar', 'GestionCarrito@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'GestionCarrito@ajaxPanelListar');
                            Route::get('/listar', 'GestionCarrito@ajaxListar');
                            
                            Route::get('/panelEditar', 'GestionCarrito@ajaxPanelEditar');
                            Route::post('/actualizar', 'GestionCarrito@ajaxActualizar');

                            Route::post('/eliminar', 'GestionCarrito@ajaxEliminar');

                            Route::get('/panelNuevo', 'GestionCarrito@ajaxPanelNuevo');
                            Route::post('/insertar', 'GestionCarrito@ajaxInsertar');
                        });
                    });
                });

                Route::prefix('/configuracion')->group(function () {
                    Route::prefix('/perfiles')->group(function () {
                        Route::get('/', 'Perfiles@index');
                        Route::get('/nuevo', 'Perfiles@index');
                        Route::get('/{iIdInterno?}/editar', 'Perfiles@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Perfiles@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Perfiles@ajaxPanelNuevo');
                            Route::post('/nuevo/listarPermisos', 'Perfiles@ajaxNuevoListarPermisos');
                            Route::get('/panelEditar', 'Perfiles@ajaxPanelEditar');
                            Route::post('/editar/listarPermisos', 'Perfiles@ajaxEditarListarPermisos');

                            Route::post('/listar', 'Perfiles@ajaxListar');
                            Route::post('/insertar', 'Perfiles@ajaxInsertar');
                            Route::post('/actualizar', 'Perfiles@ajaxActualizar');
                            Route::post('/eliminar', 'Perfiles@ajaxEliminar');
                        });
                    });

                    Route::prefix('/usuarios')->group(function () {
                        Route::get('/', 'Usuarios@index');
                        Route::get('/nuevo', 'Usuarios@index');
                        Route::get('/{iIdInterno?}/editar', 'Usuarios@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Usuarios@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Usuarios@ajaxPanelNuevo');
                            Route::post('/nuevo/listarPerfiles', 'Usuarios@ajaxNuevoListarPerfiles');
                            Route::get('/panelEditar', 'Usuarios@ajaxPanelEditar');
                            Route::post('/editar/listarPerfiles', 'Usuarios@ajaxEditarListarPerfiles');

                            Route::post('/listar', 'Usuarios@ajaxListar');
                            Route::post('/insertar', 'Usuarios@ajaxInsertar');
                            Route::post('/actualizar', 'Usuarios@ajaxActualizar');
                            Route::post('/actualizarContrasena', 'Usuarios@ajaxActualizarContrasena');
                            Route::post('/eliminar', 'Usuarios@ajaxEliminar');
                        });
                    });

                    Route::prefix('/tipos-comprobante')->group(function () {
                        Route::get('/', 'TiposComprobante@index');
                        Route::get('/nuevo', 'TiposComprobante@index');
                        Route::get('/{iIdInterno?}/editar', 'TiposComprobante@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'TiposComprobante@ajaxPanelListar');
                            Route::get('/panelNuevo', 'TiposComprobante@ajaxPanelNuevo');
                            Route::post('/nuevo/listarTiposComprobanteSunat', 'TiposComprobante@ajaxNuevoListarTiposComprobanteSunat');
                            Route::get('/panelEditar', 'TiposComprobante@ajaxPanelEditar');
                            Route::post('/editar/listarTiposComprobanteSunat', 'TiposComprobante@ajaxEditarListarTiposComprobanteSunat');

                            Route::post('/listar', 'TiposComprobante@ajaxListar');
                            Route::post('/insertar', 'TiposComprobante@ajaxInsertar');
                            Route::post('/actualizar', 'TiposComprobante@ajaxActualizar');
                            Route::post('/insertarSerie', 'TiposComprobante@ajaxInsertarSerie');
                            Route::post('/eliminarSerie', 'TiposComprobante@ajaxEliminarSerie');
                            Route::post('/eliminar', 'TiposComprobante@ajaxEliminar');
                        });
                    });

                    Route::prefix('/facturacion-electronica')->group(function () {
                        Route::get('/', 'FacturacionElectronica@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'FacturacionElectronica@ajaxListar');
                            Route::post('/actualizarUsuarioClaveSOL', 'FacturacionElectronica@ajaxActualizarUsuarioClaveSOL');
                            Route::post('/actualizarCertificadoDigital', 'FacturacionElectronica@ajaxActualizarCertificadoDigital');
                        });
                    });

                    Route::prefix('/recepcion')->group(function () {
                        Route::get('/', 'Recepcion@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::post('/listar', 'Recepcion@ajaxListar');
                            Route::post('/actualizar', 'Recepcion@ajaxActualizar');
                        });
                    });

                    Route::prefix('/agencias')->group(function () {
                        Route::get('/', 'Agencia@index');
                        Route::get('/nuevo', 'Agencia@index');
                        Route::get('/{iIdInterno?}/editar', 'Agencia@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Agencia@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Agencia@ajaxPanelNuevo');
                            Route::get('/panelEditar', 'Agencia@ajaxPanelEditar');
                            Route::post('/editar/listarPerfiles', 'Agencia@ajaxEditarListarPerfiles');

                            Route::post('/listar', 'Agencia@ajaxListar');
                            Route::post('/insertar', 'Agencia@ajaxInsertar');
                            Route::post('/actualizar', 'Agencia@ajaxActualizar');
                            Route::post('/actualizarContrasena', 'Agencia@ajaxActualizarContrasena');
                            Route::post('/eliminar', 'Agencia@ajaxEliminar');
                        });
                    });

                    Route::prefix('/publicidad')->group(function () {
                        Route::get('/', 'Publicidad@index');
                        Route::get('/nuevo', 'Publicidad@index');
                        Route::get('/{iIdInterno?}/editar', 'Publicidad@index');

                        Route::prefix('/ajax')->group(function () {
                            Route::get('/panelListar', 'Publicidad@ajaxPanelListar');
                            Route::get('/panelNuevo', 'Publicidad@ajaxPanelNuevo');
                            Route::get('/panelEditar', 'Publicidad@ajaxPanelEditar');

                            Route::post('/listar', 'Publicidad@ajaxListar');
                            Route::post('/insertar', 'Publicidad@ajaxInsertar');
                            Route::post('/actualizar', 'Publicidad@ajaxActualizar');
                            Route::post('/eliminar', 'Publicidad@ajaxEliminar');
                        });
                    });
                });
            });
        });
    });
});

Route::get('ruta', function () {

    /*$empresa = Empresa::find(1);
    $venta = Compra::find(1);
    $estado = Estado::find(1);
    $agencia = Agencia::find(1);
    $carrito = array();
    foreach($venta->detalles as $detalle)
    {
        $producto = $detalle->producto;
        $producto->cantidad = $detalle->cantidad;
        $producto->pFinal = $detalle->precio_venta;
        array_push($carrito, $producto);
    }

    return view('website.pdf.pedido',compact('venta','carrito'));*/
    /*$empresa = Empresa::find(1);
    $venta = Compra::find(23);
    $estado = Estado::find(1);
    $agencia = Agencia::find(1);
    $carrito = array();
    foreach($venta->detalles as $detalle)
    {
        $producto = $detalle->producto;
        $producto->cantidad = $detalle->cantidad;
        $producto->pFinal = $detalle->precio_venta;
        array_push($carrito, $producto);
    }

    $pdf = PDF::loadview('website.pdf.pedido',['venta' => $venta, 'carrito' => $carrito])->setPaper('a4')->setWarnings(false);
    PDF::loadView('website.pdf.pedido',['venta' => $venta, 'carrito' => $carrito])
        ->save(public_path().'/storage/pedidos/' . $venta->codigo.'.pdf');

    if($empresa->correo_pedidos)
    {
        Mail::send('website.email.pedido_empresa',compact("venta"), function ($mail) use ($pdf,$venta,$empresa) {
            $mail->to($empresa->correo_pedidos);
            $mail->subject('PEDIDO COD: '.$venta->codigo);
            $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
            $mail->from('website@ecovalle.pe','ECOVALLE');
        });
    }

    if($empresa->correo_pedidos_1)
    {
        Mail::send('website.email.pedido_empresa',compact("venta"), function ($mail) use ($pdf,$venta,$empresa) {
            $mail->to($empresa->correo_pedidos_1);
            $mail->subject('PEDIDO COD: '.$venta->codigo);
            $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
            $mail->from('website@ecovalle.pe','ECOVALLE');
        });

        Mail::send('website.email.pedido_empresa',compact("venta"), function ($mail) use ($pdf,$venta,$empresa) {
            $mail->to('ccubas@unitru.edu.pe');
            $mail->subject('PEDIDO COD: '.$venta->codigo);
            $mail->attachdata($pdf->output(), $venta->codigo.'.pdf');
            $mail->from('website@ecovalle.pe','ECOVALLE');
        });
    }
    
    if($empresa->telefono_pedidos)
    {
        $result = enviapedido($venta, $empresa->telefono_pedidos);
    }

    if($empresa->telefono_pedidos_1)
    {
        $result = enviapedido($venta, $empresa->telefono_pedidos_1);
    }*/

    $ventas = Compra::with(['detalles'])->get();

    foreach($ventas as $venta)
    {
        $descuento = 0;
        $des = $venta->descuento;
        foreach($venta->detalles as $detalle)
        {
            $fdes = ($detalle->precio_actual - $detalle->precio_venta) * $detalle->cantidad;
            $descuento = $descuento + $fdes;
        }
        $venta->descuento = $des + number_format(round(($descuento * 10) / 10, 1), 2);
        $venta->update();
    }
    return $ventas;
});
