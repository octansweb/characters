<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CharactersController;
use App\Http\Controllers\CharacterChatController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/characters/create', [CharactersController::class, 'create'])->middleware(['auth', 'verified'])->name('characters.create');
Route::get('/characters', [CharactersController::class, 'index'])->middleware(['auth', 'verified'])->name('characters.index');
Route::post('/characters', [CharactersController::class, 'store'])->middleware(['auth', 'verified'])->name('characters.store');
Route::get('/characters/{character}/chat', [CharacterChatController::class, 'show'])->middleware(['auth', 'verified'])->name('characters.chat.show');
Route::post('/characters/{character}/stream', [CharacterChatController::class, 'stream'])->name('characters.stream');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
