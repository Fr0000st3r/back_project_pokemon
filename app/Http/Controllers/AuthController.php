<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // dd("entro");

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Sanctum crea y maneja el token con expiración de 5 minutos
        $token = $user->createToken('auth_token', ['*'], now()->addMinutes(5))->plainTextToken;

        Bitacora::create([
            'user_id' => $user->id,
            'action' => 'login',
            'module' => 'auth',
            'created_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Sanctum elimina el token actual
        $request->user()->currentAccessToken()->delete();

        Bitacora::create([
            'user_id' => $request->user()->id,
            'action' => 'logout',
            'module' => 'auth',
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Sesión cerrada']);
    }
}