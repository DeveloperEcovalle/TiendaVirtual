<?php

namespace App\Http\Controllers\Intranet;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Respuesta;
use App\Http\Controllers\Result;
use App\Menu;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Intranet extends Controller {

    protected $usuario;
    protected $perfil;

    protected $iModuloId;
    protected $iMenuId;
    protected $sPermisoListar;
    protected $sPermisoInsertar;
    protected $sPermisoActualizar;
    protected $sPermisoEliminar;

    public function init() {
        $this->usuario = session()->get('usuario');
        $this->perfil = $this->usuario->perfil;
        $this->perfil->refresh();
    }

    public function ajaxListarMenus(Request $request) {
        $this->init();

        global $iPerfilId;
        $iPerfilId = $this->perfil->id;

        $lstModulos = Menu::whereHas('submenus', function (Builder $submenu) {
            $submenu->whereHas('permisos', function (Builder $permiso) {
                $permiso->whereHas('perfilespermisos', function (Builder $perfilPermiso) {
                    $perfilPermiso->where('perfil_id', $GLOBALS['iPerfilId']);
                });
            });
        })->with(['submenus' => function ($submenu) {
            $submenu->whereHas('permisos', function (Builder $permiso) {
                $permiso->whereHas('perfilespermisos', function (Builder $perfilPermiso) {
                    $perfilPermiso->where('perfil_id', $GLOBALS['iPerfilId']);
                });
            })->orderBy('orden', 'asc');
        }])->orderBy('orden', 'asc')->get();

        $iModuloId = $request->get('iModuloId');

        $lstMenus = Menu::whereHas('permisos', function (Builder $permiso) {
            $permiso->whereHas('perfilespermisos', function (Builder $perfilPermiso) {
                $perfilPermiso->where('perfil_id', $GLOBALS['iPerfilId']);
            });
        })->where('menu_id', $iModuloId)->orderBy('orden')->get();

        $respuesta = new Respuesta;
        $respuesta->result = Result::SUCCESS;
        $respuesta->data = [
            'lstModulos' => $lstModulos,
            'lstMenus' => $lstMenus
        ];

        return response()->json($respuesta);
    }
}
