<?php

use Inertia\Inertia;
use Aws\Polly\PollyClient;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CharactersController;
use App\Http\Controllers\GoogleLoginController;
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

Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::get('/test-polly', function () {
    $client = new PollyClient([
        'region' => env('AWS_DEFAULT_REGION'),
        'version' => 'latest',
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ]
    ]);

    $preSignedUrl = $client->createSynthesizeSpeechPreSignedUrl([
        'OutputFormat' => 'mp3',
        'Text' => 'Hello, this is a test message.',
        'VoiceId' => 'Matthew',
        'Engine' => 'generative',
    ]);
    
    echo "Pre-signed URL: $preSignedUrl";
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
