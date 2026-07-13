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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda dinonaktifkan. Silakan hubungi Admin.',
            ]);
        }

        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // If user has a role, redirect to their respective dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        } elseif ($user->role === 'kurir') {
            return redirect()->route('kurir.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        } else {
            return redirect()->route('customer.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }
}
