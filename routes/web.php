<?php

use App\Models\Torneo;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\EquipoController;
use App\Http\Controllers\TorneoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\JugadorController;

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

Route::get('/', [TorneoController::class, 'landing'])->name('landing');
Route::get('/torneos', [TorneoController::class, 'index'])->name('torneos');

Route::get('/login',[UsuarioController::class, 'showLogin'])->name('showLogin');
Route::post('/login',[UsuarioController::class, 'login'])->name('login');

Route::get('/register',[UsuarioController::class, 'showRegister'])->name('showRegister');
Route::post('/register',[UsuarioController::class, 'register'])->name('register');

Route::get('/usuario/newJugador',[JugadorController::class, 'showNewJugador'])->name('showNewJugador');
Route::post('/usuario/newJugador',[JugadorController::class, 'newJugador'])->name('newJugador');

Route::get('/MercadoJugadores', [JugadorController::class, 'getJugadores']);
Route::get('/MercadoJugadores/{id}', [JugadorController::class, 'getJugador']);

Route::get('/jugadores', [JugadorController::class, 'index'])->name('jugadores');

Route::get('/logout',[UsuarioController::class, 'logout'])->name('logout');

// route::get('/torneos/individual/{id}', [TorneoController::class, 'getTorneoIndividual'])->name('getTorneoIndividual');
Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos');

route::get('/torneos/individual/{id}', function($id){
    return view('torneos.individual', ['id' => $id]);
})->name('TorneoIndividual');

// crear equipos
Route::get('/equipos/crear', [EquipoController::class, 'create'])->name('equipos.create');
Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');


Route::get('/equipos/{id}', function($id){
    return view('equipos.jugadores', ['id' => $id]);
})->name('mostrarJugadores');

Route::get('/torneos/individual/{id}/bracket', function ($id) {
    return view('bracket.bracket', ['torneoId' => $id]);
})->name('bracket.show');

Route::get('/torneos/general', function () {
    return view('torneos.general');
})->name('torneos.general');

Route::middleware(['auth'])->group(function () {
    Route::get('landing', function () {
        $user = Auth::user();

        return view('landing', compact('user'));
    });
});

Route::post('/contratar-jugador', [JugadorController::class, 'contratarJugador']);

// editar usuario
Route::get('/editar', [UsuarioController::class, 'showEdit'])->name('showEdit');
Route::post('/editar', [UsuarioController::class, 'editar'])->name('editar');

// Profile routes
Route::get('/profile', [UsuarioController::class, 'showProfile'])->name('profile');
Route::get('/api/profile', [UsuarioController::class, 'getProfileData'])->name('profile.data');

// Route::get('/', function () {
//     return view('landing');
// });

