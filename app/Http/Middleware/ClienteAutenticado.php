<?php

namespace App\Http\Middleware;

use Closure;

class ClienteAutenticado {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if ($request->session()->has('cliente')) {
            return redirect('/');
        }

        return $next($request);
    }
}
