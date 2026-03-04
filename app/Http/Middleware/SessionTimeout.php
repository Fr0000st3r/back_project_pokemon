<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class SessionTimeout
{
    /**
     * Tiempo de inactividad permitido en minutos.
     */
    protected int $timeoutMinutes = 5;

    /**
     * Verifica si el token ha expirado por inactividad.
     * Si sigue activo, renueva la expiración del token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()?->currentAccessToken();

        if (!$token || !($token instanceof PersonalAccessToken)) {
            return response()->json([
                'message' => 'Sesión expirada por inactividad',
                'session_expired' => true,
            ], 401);
        }

        // Verificar si el token tiene fecha de expiración y ya venció
        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();

            return response()->json([
                'message' => 'Sesión expirada por inactividad',
                'session_expired' => true,
            ], 401);
        }

        // Si el token sigue activo, renovar la expiración
        $token->forceFill([
            'expires_at' => now()->addMinutes($this->timeoutMinutes),
        ])->save();

        return $next($request);
    }
}
