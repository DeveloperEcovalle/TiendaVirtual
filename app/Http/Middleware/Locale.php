<?php

namespace App\Http\Middleware;

use Closure;

class Locale {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!$request->session()->has('locale')) {
            $request->session()->put('locale', 'es');
        }
        return $next($request);
    }
}
