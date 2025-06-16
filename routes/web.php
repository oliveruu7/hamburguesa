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

/* ---------- LOGIN ---------- */
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login',  [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'verify'])->name('login.verify');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ---------- ÁREA PROTEGIDA ---------- */
Route::middleware('auth')->group(function () {

    Route::get('/admin', fn () => view('layouts.admin'))
        ->name('admin')
        ->middleware(CheckPermission::class . ':main.menu.view');

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
    
    Route::middleware(CheckPermission::class . ':sales.index')->group(function () {
        Route::resource('ventas', SaleController::class)
             ->parameters(['ventas' => 'sale'])   // importante para el binding con el modelo `Venta`
             ->names('sales');                     // para usar names como sales.index, sales.create, etc.
    });

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

