<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function update(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update the user's name
        $user->name = $validatedData['name'];
        $user->save();

        // Return a response
        return response()->json([
            'message' => 'Your name has been updated successfully.',
            'user' => $user,
        ], 200);
    }
}
