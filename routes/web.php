<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrinterController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->group( function() {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::view('cajones', 'cajones')->middleware('permission:cajones_index');
    Route::view('tipos', 'tipos')->middleware('permission:tipos_index');
    Route::view('movimientos', 'movimientos')->middleware('permission:movimientos_index');
    Route::view('tarifas', 'tarifas')->middleware('permission:tarifas_index');
    Route::view('empresa', 'empresa')->middleware('permission:empresa_index');
    Route::view('usuarios', 'usuarios')->middleware('permission:usuarios_index');
    Route::view('rentas', 'rentas')->middleware('permission:rentas_index');
    Route::view('ventasdiarias', 'ventasdiarias')->middleware('permission:reporte_ventasdiarias_index');
    Route::view('cortes', 'cortes')->middleware('permission:cortes_index');
    Route::view('ventasporfechas', 'ventas-por-fechas')->middleware('permission:reporte_ventasporfecha_index');
    Route::view('proximasrentas', 'proximas-rentas')->middleware('permission:reporte_rentasavencer_index');
    Route::view('extraviados', 'extraviados')->middleware('permission:extraviados_index');
    Route::view('permisos', 'permisos')->middleware('permission:roles_index');
});

//rutas de impresión
Route::get('print/order/{id}', [PrinterController::class, "TicketVista"]);
Route::get('ticket/pension/{id}', [PrinterController::class, "TicketPension"]);
