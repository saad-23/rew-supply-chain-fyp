<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Allow access only to users whose role is in the $roles list.
     * Also blocks inactive accounts.
     *
     * Usage in routes: middleware('role:admin,staff')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Block inactive accounts
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been deactivated. Contact the administrator.']);
        }

        // Check role — treat 'manager' same as 'staff'
        $effectiveRole = in_array($user->role, ['staff', 'manager']) ? 'staff' : $user->role;

        if (!in_array($effectiveRole, $roles) && !in_array($user->role, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
