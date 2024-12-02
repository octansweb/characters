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
            'avatar_url' => 'required|string',
            'description' => 'nullable|string',
            'personality' => 'nullable|string',
            'is_public' => 'required|boolean',
            'gender' => 'required|string|in:Male,Female',
            'voice' => 'required|string|in:Olivia,Amy,Danielle,Joanna,Matthew,Ruth,Stephen',
        ]);

        // Generate a random string for the email
        $randomString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8);
        $uniqueId = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        $randomPassword = bin2hex(random_bytes(8));

        $user = User::create([
            'name' => 'User_' . $uniqueId,
            'email' => $randomString . $uniqueId . '@tempdomain.example',
            'password' => Hash::make($randomPassword),
        ]);

        // Create a character for them
        $user->characters()->create([
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
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
