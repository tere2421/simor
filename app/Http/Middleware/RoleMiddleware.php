<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Contoh pemakaian di route:
     *   ->middleware('role:SM')          hanya SM
     *   ->middleware('role:SM,PIC')      SM atau PIC
     *   ->middleware('role:SM,PIC,Staff') semua role
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Akses ditolak. Role kamu tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}
