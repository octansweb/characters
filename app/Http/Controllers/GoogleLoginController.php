<?php

namespace App\Http\Controllers;

use App\Models\User;
use Jenssegers\Agent\Agent;
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
        $agent = new Agent();

        // Check if the user already exists by email:
        $existingUser = User::whereEmail($user->email)->first();
        if ($existingUser) {
            // If we already have the user, log them in and redirect.
            Auth::login($existingUser);
        } else {
            // Register a new user with random password
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => Hash::make(rand(100000, 999999)),
            ]);
            $newUser->markEmailAsVerified();
            Auth::login($newUser);
        }

        // Check if the user is on a mobile device
        if ($agent->isMobile()) {
            // Redirect to the mobile URL from the environment file
            return redirect()->away(env('GOOGLE_MOBILE_CALLBACK'));
        }

        // Otherwise, redirect to the dashboard
        return redirect(route('dashboard'));
    }
}
