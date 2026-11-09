<?php

use App\Http\Controllers\ArquitecturaController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CatalogoApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProyectoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/catalogo', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/catalogo/crear', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/catalogo', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/catalogo/{producto}', [ProductoController::class, 'show'])->name('productos.show');
Route::get('/catalogo/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
Route::put('/catalogo/{producto}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/catalogo/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
Route::get('/proyectos/crear', [ProyectoController::class, 'create'])->name('proyectos.create');
Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'show'])->name('proyectos.show');
Route::get('/proyectos/{proyecto}/editar', [ProyectoController::class, 'edit'])->name('proyectos.edit');
Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'update'])->name('proyectos.update');
Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');

Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/anadir', [CarritoController::class, 'anadir'])->name('carrito.anadir');
Route::post('/carrito/quitar', [CarritoController::class, 'quitar'])->name('carrito.quitar');
Route::post('/carrito/limpiar', [CarritoController::class, 'limpiar'])->name('carrito.limpiar');

Route::get('/presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos.index');
Route::post('/presupuestos', [PresupuestoController::class, 'store'])->name('presupuestos.store');
Route::get('/presupuestos/{presupuesto}', [PresupuestoController::class, 'show'])->name('presupuestos.show');

Route::get('/arquitecturas', [ArquitecturaController::class, 'index'])->name('arquitecturas.index');
Route::post('/arquitecturas', [ArquitecturaController::class, 'store'])->name('arquitecturas.store');
Route::get('/arquitecturas/{arquitectura}', [ArquitecturaController::class, 'show'])->name('arquitecturas.show');

Route::get('/api/regiones', [CatalogoApiController::class, 'regiones'])->name('api.regiones');
Route::get('/api/tipos', [CatalogoApiController::class, 'tipos'])->name('api.tipos');
Route::get('/api/productos', [CatalogoApiController::class, 'productos'])->name('api.productos');
