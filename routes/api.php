<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\MerekController;
use App\Http\Controllers\Api\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::get('refresh', [AuthController::class, 'refresh']);
    Route::get('logout', [AuthController::class, 'logout']);

    Route::prefix('/kendaraan')->group(function () {
    Route::get('', [KendaraanController::class, 'index']);
    Route::post('', [KendaraanController::class, 'create']);
    Route::get('/{id}', [KendaraanController::class, 'detail']);
    Route::put('/{id}', [KendaraanController::class, 'update']);
    Route::delete('/{id}', [KendaraanController::class, 'delete']);
    // Route::get('/merek/{merek_id}', [KendaraanController::class, 'getByMerek']);
    // Route::get('/kategori/{kategori_id}', [KendaraanController::class, 'getByKategori']);
    });

#Merek Routes
    Route::prefix('/merek')->group(function () {
    Route::get('', [MerekController::class, 'index']);       
    Route::post('', [MerekController::class, 'create']);     
    Route::get('/{id}', [MerekController::class, 'detail']); 
    Route::put('/{id}', [MerekController::class, 'update']);  
    Route::delete('/{id}', [MerekController::class, 'delete']); 
    Route::get('/{merek_id}/kendaraan', [KendaraanController::class, 'getByMerek']);
    });

#Kategori Routes
    Route::prefix('/kategori')->group(function () {
    Route::get('', [KategoriController::class, 'index']);        
    Route::post('', [KategoriController::class, 'create']);      
    Route::get('/{id}', [KategoriController::class, 'detail']);  
    Route::put('/{id}', [KategoriController::class, 'update']);  
    Route::delete('/{id}', [KategoriController::class, 'delete']); 
    Route::get('/{kategori_id}/kendaraan', [KendaraanController::class, 'getByKategori']);
    });

#Transaksi Routes
    Route::prefix('transaksi')->group(function () {
    Route::get('/', [TransaksiController::class, 'index']);           
    Route::post('/', [TransaksiController::class, 'create']);        
    Route::get('/{id}', [TransaksiController::class, 'detail']);     
    Route::put('/{id}', [TransaksiController::class, 'update']);     
    Route::delete('/{id}', [TransaksiController::class, 'delete']); 
    });
});
