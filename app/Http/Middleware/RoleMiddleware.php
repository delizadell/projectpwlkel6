<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu!');
        }

        $user = Auth::user();
        $activeRole = $user->getActiveRole();

        // 1. Jika peran aktif saat ini cocok dengan yang diizinkan rute
        if (in_array($activeRole, $roles, true)) {
            return $next($request);
        }

        // 2. Transisi mulus untuk akun non-dosen (Role Switcher otomatis saat berpindah modul)
        if ($user->canSwitchRole()) {
            if (in_array('asisten_dosen', $roles, true)) {
                session(['active_role' => 'asisten_dosen']);

                return $next($request);
            }

            if (in_array('mahasiswa', $roles, true)) {
                session(['active_role' => 'mahasiswa']);

                return $next($request);
            }
        }

        // 3. Jika ditolak (misal Dosen mencoba akses halaman mahasiswa/asprak atau sebaliknya)
        return redirect()->route($user->getDashboardRoute())->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut!');

        return $next($request);
    }
}
