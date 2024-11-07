<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;

class GoogleLoginController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }


    public function handleGoogleCallback(Request $request)
    {
        $user = Socialite::driver('google')->user();

        // Check if the user already exists by email:
        $existingUser = User::whereEmail($user->email)->first();
        if ($existingUser) {
            // If we already have the user, simply log them in and redirect.
            Auth::login($existingUser);
            return redirect(route('dashboard'));
        }

        // Register a new user with random password
        $newUser = User::create(['name' => $user->name, 'email' => $user->email, 'password' => Hash::make(rand(100000, 999999))]);
        Auth::login($newUser);

        return redirect(route('dashboard'));
    }
}
