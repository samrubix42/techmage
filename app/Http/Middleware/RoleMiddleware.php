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
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive.']);
        }

        if ($user->role !== $role) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isEmployee()) {
                return redirect()->route('employee.dashboard');
            }

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
