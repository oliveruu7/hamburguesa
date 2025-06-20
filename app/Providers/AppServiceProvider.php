<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;   // ← importa Blade
use Illuminate\Support\Facades\Auth;    // ← importa Auth
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios de aplicación.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa servicios de aplicación.
     */
    public function boot(): void
    {
        
        /*
        |--------------------------------------------------------------
        | Directiva Blade: @permiso('nombre.permiso')
        |--------------------------------------------------------------
        | Ejemplo en un Blade:
        |   @permiso('sales.index')
        |       <li><a href="{{ route('sales.index') }}">Ventas</a></li>
        |   @endpermiso
        |
        | La directiva devuelve true si el usuario autenticado pertenece
        | a un rol que tiene ese permiso.
        */
        Blade::if('permiso', function (string $nombre) {
            return Auth::check() &&
                   Auth::user()->rol &&
                   Auth::user()->rol
                       ->permisos()
                       ->where('nombre', $nombre)
                       ->exists();
        });
        Paginator::useBootstrapFive();
    }
}
