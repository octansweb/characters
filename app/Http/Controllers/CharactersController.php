<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Character;
use Illuminate\Http\Request;

class CharactersController extends Controller
{
    public function create()
    {
        return Inertia::render('Characters/Create');
    }

    public function index(Request $request)
    {
        $characters = $request->user()->characters;

        return Inertia::render('Characters/Index', compact('characters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',  // Updated validation rule
            'description' => 'nullable|string',
            'personality' => 'nullable|string',
            'is_public' => 'required|boolean',
            'gender' => 'required|string|in:Male,Female',  // Added gender validation
            'voice' => 'required|string|in:Olivia,Amy,Danielle,Joanna,Matthew,Ruth,Stephen'
        ]);
    
        $avatar_url = null;
    
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $avatar_url = asset('storage/' . $avatarPath);
        }
    
        Character::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'avatar_url' => $avatar_url,
            'description' => $request->description,
            'personality' => $request->personality,
            'is_public' => $request->is_public,
            'gender' => $request->gender,  // Save gender
            'voice' => $request->voice,
        ]);
    
        return redirect()->route('characters.index')->with('success', 'Character created successfully.');
    }
    
}
