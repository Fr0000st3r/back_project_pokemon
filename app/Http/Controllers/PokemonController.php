<?php

namespace App\Http\Controllers;

use App\Models\Pokemon;
use App\Models\Bitacora;
use App\Models\Session;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        Bitacora::create([
            'user_id' => $user->id,
            'action' => 'consultar',
            'module' => 'pokemons',
            'created_at' => now(),
        ]);

        $pokemons = Pokemon::paginate(3);
        return response()->json($pokemons);
    }

    // Registrar nuevo pokémon
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'color' => 'required',
            'attributes' => 'required',
            'category' => 'required',
            'image' => 'required|image|mimes:jpg,png|max:1024',
        ]);

        // Guardar imagen en storage
        $path = $request->file('image')->store('pokemons', 'public');

        $user = $request->user();

        $pokemon = Pokemon::create([
            'name' => $request->name,
            'color' => $request->color,
            'attributes' => $request->input('attributes'),
            'category' => $request->category,
            'image_path' => $path,
        ]);

        Bitacora::create([
            'user_id' => $user->id,
            'action' => 'insertar',
            'module' => 'pokemons',
            'created_at' => now(),
        ]);

        return response()->json($pokemon, 201);
    }
}
