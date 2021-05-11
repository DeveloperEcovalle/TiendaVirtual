<?php

namespace App\Http\Middleware;

use App\Menu;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class UsuarioAutenticado {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->session()->has('usuario')) {
            $usuario = $request->session()->get('usuario');

            global $iPerfilId;
            $iPerfilId = $usuario->perfil->id;

            $lstMenus = Menu::whereHas('permisos', function (Builder $permiso) {
                $permiso->whereHas('perfilespermisos', function (Builder $perfilPermiso) {
                    $perfilPermiso->where('perfil_id', $GLOBALS['iPerfilId']);
                });
            })->get();

            $primerMenu = $lstMenus[0];
            return redirect($primerMenu->enlace);
        }

        return $next($request);
    }
}
