<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ControlPanelController;
use App\Http\Controllers\MovieImportController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CollectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Todas estas rutas requieren que el usuario haya iniciado sesión
Route::middleware('auth')->group(function () {

    // --- PERFIL DE USUARIO ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- PANEL DE CONTROL ---
    //verified por si usamos la verificación de correo)
    Route::get('/controlPanel', [ControlPanelController::class, 'index'])->middleware('verified')->name('controlPanel');

    // --- IMPORTACIÓN DE PELÍCULAS ---
    Route::get('/search-movies', [MovieImportController::class, 'index'])->name('movies.import.index');
    Route::post('/search-movies/results', [MovieImportController::class, 'search'])->name('movies.import.search');
    Route::post('/search-movies/store', [MovieImportController::class, 'store'])->name('movies.import.store');

    // --- GESTIÓN DE PELÍCULAS ---
    Route::get('/dashboard', [MovieController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-movies', [MovieController::class, 'index'])->name('my.movies');
    Route::post('/my-movies', [MovieController::class, 'store'])->name('my.movies.store');
    Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
    Route::post('/my-movies/{movie}/status', [MovieController::class, 'updateStatus'])->name('my.movies.status');
    Route::delete('/my-movies/{movie}', [MovieController::class, 'destroy'])->name('my.movies.destroy');
    Route::delete('/movies/{movie}/global-delete', [MovieController::class, 'globalDestroy'])->name('movies.global.destroy');
    
    // --- RESEÑAS ---
    Route::post('/movies/{movie}/review', [MovieController::class, 'storeReview'])->name('movies.review.store');
    Route::post('/reviews/{review}/toggle', [MovieController::class, 'toggleReview'])->name('reviews.toggle');
    Route::post('/movies/{movie}/add-to-collection', [MovieController::class, 'addToCollection'])->name('movies.add-to-collection');

    // --- COLECCIONES ---
    Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
    Route::post('/collections', [CollectionController::class, 'store'])->name('collections.store');
    Route::post('/collections/{collection}/toggle', [CollectionController::class, 'togglePrivacy'])->name('collections.toggle');
    Route::delete('/collections/{collection}', [CollectionController::class, 'destroy'])->name('collections.destroy');
    Route::delete('/collections/{collection}/movies/{movie}', [CollectionController::class, 'removeMovie'])->name('collections.remove-movie');
});

require __DIR__ . '/auth.php';