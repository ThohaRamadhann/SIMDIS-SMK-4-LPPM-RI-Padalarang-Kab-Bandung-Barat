<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // ambil user dari guard bawaan Laravel
        $user = Auth::user();  // <-- ini akan otomatis mengambil model Pengguna

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // ambil nama_role dari relasi Role model
        $userRole = optional($user->role)->nama_role;

        // cek apakah role user ada dalam daftar role yg diizinkan
        if (! in_array($userRole, $roles)) {
            abort(403, 'Forbidden: You do not have permission to access this page.');
        }

        return $next($request);
    }
}
