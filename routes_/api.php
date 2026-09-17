<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiLogController;
use App\Http\Controllers\ApiDescargarController;
//use App\Models\funcionarios;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('entrarapp/auth_', [App\Http\Controllers\ApiLogController::class, 'EntrarApp'])->name('entrarapp');       
Route::post('usuarios_/auth_', [App\Http\Controllers\ApiLogController::class, 'Usuarios_'])->name('usuarios_')->middleware('auth_:sanctum');
Route::post('salirapp/auth_', [App\Http\Controllers\ApiLogController::class, 'SalirApp'])->name('salirapp')->middleware('auth_:sanctum');

Route::post('usuarios/descargar', [App\Http\Controllers\ApiDescargarController::class, 'DescargaUsuarios'])->name('descargarusuarios')->middleware('auth_:sanctum');
Route::post('funcionarios/descargar', [App\Http\Controllers\ApiDescargarController::class, 'DescargaFuncionarios'])->name('descargarfuncionarios')->middleware('auth_:sanctum');
Route::post('elfuncionario/descargar', [App\Http\Controllers\ApiDescargarController::class, 'DescargaelFuncionario'])->name('descargarelfuncionario')->middleware('auth_:sanctum');
Route::post('asistencia/descargar', [App\Http\Controllers\ApiDescargarController::class, 'DescargaAsistencia'])->name('descargarasistencia')->middleware('auth_:sanctum');
Route::get('sincronizar/descargar', [App\Http\Controllers\ApiDescargarController::class, 'Sincronizar'])->name('sincroizar');
