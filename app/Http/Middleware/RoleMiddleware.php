<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Belum login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Cek apakah role user ada di daftar role yang diizinkan
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Tidak punya akses');
        }

        return $next($request);
    }
}
