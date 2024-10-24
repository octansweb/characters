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
            'avatar_url' => 'nullable|url',
            'description' => 'nullable|string',
            'personality' => 'nullable|string',
            'is_public' => 'required|boolean',
        ]);

        Character::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'description' => $request->description,
            'personality' => $request->personality,
            'is_public' => $request->is_public,
        ]);

        return redirect()->route('characters.index')->with('success', 'Character created successfully.');
    }
}
