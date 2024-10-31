<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Character;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CharactersController extends Controller
{
    public function index(Request $request)
    {
        $characters = $request->user()->characters;

        return response()->json([
            'success' => true,
            'data' => $characters
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'personality' => 'nullable|string',
            'is_public' => 'required|boolean',
            'gender' => 'required|string|in:Male,Female',
            'voice' => 'required|string|in:Olivia,Amy,Danielle,Joanna,Matthew,Ruth,Stephen',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $avatar_url = null;
    
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $avatar_url = asset('storage/' . $avatarPath);
        }
    
        $character = Character::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'avatar_url' => $avatar_url,
            'description' => $request->description,
            'personality' => $request->personality,
            'is_public' => $request->is_public,
            'gender' => $request->gender,
            'voice' => $request->voice,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Character created successfully',
            'data' => $character
        ], 201);
    }
    
}
