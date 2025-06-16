<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Intercepta la petición y verifica si el usuario posee el permiso $permiso.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permiso  nombre del permiso que requiere la ruta
     */
    public function handle($request, Closure $next, string $permiso)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        /* ------------- Acceso total opcional -------------
           Si en tu tabla rol_usuario tienes un campo 'full_access'
           que vale 1 para Administrador, descomenta lo siguiente:

        if ($user->rol && $user->rol->full_access) {
            return $next($request);
        }
        --------------------------------------------------- */

        // ¿El rol del usuario posee el permiso solicitado?
        $tienePermiso = $user->rol
            ? $user->rol->permisos()->where('nombre', $permiso)->exists()
            : false;

        if ($tienePermiso) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
