<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Middleware\CheckPermission;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\InsumoController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\SalidaController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\ProveedorController;

/* ---------- LOGIN ---------- */
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'verify'])->name('login.verify');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ---------- ÁREA PROTEGIDA ---------- */
Route::middleware('auth')->group(function () {

    // Panel principal (HTML)
    Route::get('/admin', [DashboardController::class, 'index'])
         ->name('admin')
         ->middleware(CheckPermission::class.':main.menu.view');

    // End-points en JSON que consumirá el JS
    Route::prefix('dashboard')->group(function () {
        Route::get('/metrics', [DashboardController::class, 'metrics'])
             ->name('dashboard.metrics');   // /dashboard/metrics
        Route::get('/kpis',    [DashboardController::class, 'kpis'])
             ->name('dashboard.kpis');      // /dashboard/kpis
    });


    Route::middleware(CheckPermission::class . ':users.index')->group(function () {
        Route::resource('usuarios', UserController::class);
        
    });

    Route::middleware(CheckPermission::class . ':roles.index')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware(CheckPermission::class.':products.index')->group(function () {
        Route::resource('productos', ProductController::class)
             ->parameters(['productos' => 'product'])   // importante para binding
             ->names('products');                       // mantiene names = products.*
    });

    // Rutas de ventas modificada para que funcione correctamente
    Route::resource('ventas', SaleController::class)
      ->parameters(['ventas'=>'sale'])
      ->names('sales');
    

    Route::middleware(CheckPermission::class . ':clientes.index')->group(function () {
        Route::resource('clientes', ClienteController::class)
             ->parameters(['clientes' => 'cliente'])
             ->names('clientes');
    });
    
    Route::middleware(CheckPermission::class . ':insumos.index')->group(function () {
        Route::resource('insumos', InsumoController::class)
             ->parameters(['insumos' => 'insumo'])   // importante para el binding con el modelo `Insumo`
             ->names('insumos');                     // para usar names como insumos.index, insumos.create, etc.
    });

    Route::middleware(CheckPermission::class . ':proveedores.index')->group(function () {
        Route::resource('proveedores', ProveedorController::class)
             ->parameters(['proveedores' => 'proveedor'])
             ->names('proveedores');
    });

    Route::middleware(CheckPermission::class . ':recetas.index')->group(function () {
    Route::resource('recetas', RecetaController::class)
         ->parameters(['recetas' => 'receta'])
         ->names('recetas');
});

Route::middleware(CheckPermission::class . ':compras.index')->group(function () {
    Route::resource('compras', CompraController::class)
         ->parameters(['compras' => 'compra'])
         ->names('compras');
});

Route::middleware(CheckPermission::class.':salidas.index')->group(function () {
    Route::resource('salidas', SalidaController::class)
         ->parameters(['salidas'=>'salida'])
         ->names('salidas');
    
});


});

