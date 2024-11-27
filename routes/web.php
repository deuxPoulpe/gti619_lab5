<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Routes d'authentification
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Groupe de routes avec middleware 'auth'
Route::middleware(['auth'])->group(function () {
    // Routes pour le ClientController
    Route::resource('client', ClientController::class);
    Route::get('client/{id}/edit', [ClientController::class, 'edit'])->name('client.edit');
    Route::put('client/{id}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('client/{id}', [ClientController::class, 'destroy'])->name('client.destroy');

    // Route pour le tableau de bord
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth', 'role:Administrateur'])->group(function () {
    Route::get('/admin/security-settings', [AdminController::class, 'securitySettings'])->name('admin.security-settings');
});

Route::middleware(['auth', 'role:Administrateur|Préposé aux clients résidentiels'])->group(function () {
    Route::get('/clients/residential', [ClientController::class, 'residentialClients'])->name('clients.residential');
});

Route::middleware(['auth', 'role:Administrateur|Préposé aux clients d’affaire'])->group(function () {
    Route::get('/clients/business', [ClientController::class, 'businessClients'])->name('clients.business');
});