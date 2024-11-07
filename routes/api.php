<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UsersController;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\API\CharactersController;
use App\Http\Controllers\API\RegistrationController;
use App\Http\Controllers\API\CharacterChatController;


Route::post('/sanctum/token', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    return [
        'token' => $user->createToken($request->device_name)->plainTextToken
    ];
});

Route::post('/register', [RegistrationController::class, 'store']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::patch('/user', [UsersController::class, 'update']);
    Route::get('/characters', [CharactersController::class, 'index']);
    Route::post('/characters', [CharactersController::class, 'store']);

    Route::get('/characters/{character}/chat', [CharacterChatController::class, 'show']);
    Route::post('/characters/{character}/stream', [CharacterChatController::class, 'stream']);
});