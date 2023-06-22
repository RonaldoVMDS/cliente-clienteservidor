<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CadastroController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OccurrenceController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [OccurrenceController::class, 'index']);
Route::get('/occurrence', [OccurrenceController::class, 'index'])->name('occurrences');
Route::post('/occurrences/users/{id}', [OccurrenceController::class, 'getUserOccurrences']);
Route::post('/occurrences', [OccurrenceController::class, 'store'])->name('ocorrencias.store');
Route::post('/user/{id}', [UserController::class, 'update']);
Route::post('/user/remove/{id}', [UserController::class, 'delete']);
Route::post('/occurrences/delete', [OccurrenceController::class, 'delete'])->name('ocorrencias.delete');
Route::post('/occurrences/update', [OccurrenceController::class, 'update'])->name('ocorrencias.update');
Route::get('/cadastro', function () {
    return view('cadastro');
});

Route::get('/login', function () {
    return view('login');
});
Route::get('/erro', function () {
    return view('erro');
});
Route::post('/cadastro', [UserController::class, 'cadastrar']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
