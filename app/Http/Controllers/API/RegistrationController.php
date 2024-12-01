<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules\Password;
use App\Notifications\DeepLinkedVerifyEmail;

class RegistrationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // event(new Registered($user)); // Sends the email verification link

        // $user->sendEmailVerificationNotification();
        // $user->createToken($request->device_name)->plainTextToken;

        $user->notify(new DeepLinkedVerifyEmail($user));

        return response()->json([
            'message' => 'Registration successful! Please check your email to verify your account.',
        ], 201);
    }

    public function storeCharacter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'personality' => 'nullable|string',
            'is_public' => 'required|boolean',
            'gender' => 'required|string|in:Male,Female',
            'voice' => 'required|string|in:Olivia,Amy,Danielle,Joanna,Matthew,Ruth,Stephen',
        ]);

        // Register a new user with "temporary/auto-generated" name, email and password
        $user = User::create([
            'name' => \fake()->name(),
            'email' => \fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
        ]);

        // Create a character for them
        $user->characters()->create([
            'name' => $request->name,
            'avatar_url' => $request->avatar,
            'description' => $request->description,
            'personality' => $request->personality,
            'is_public' => $request->is_public,
            'gender' => $request->gender,  // Save gender
            'voice' => $request->voice,
        ]);

        // Then generate a sanctum token for them
        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }
}
