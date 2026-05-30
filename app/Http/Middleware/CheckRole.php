<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user=$request->user(); // se captura todos los valores del usuario autenticado
        if(!$user || !$user->role || !in_array($user->role->name, $roles)){
            //no tiene acceso si no cumple y debe salir
            abort(403,'No autorizado');
        }

        return $next($request);
    }
}
