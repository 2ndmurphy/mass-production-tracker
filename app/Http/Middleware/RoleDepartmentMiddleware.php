<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleDepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $role = null, $department = null)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        if ($role && strtolower($user->role->name) !== strtolower($role)) {
            abort(403, 'Unauthorized role');
        }

        if ($department && strtolower(optional($user->department)->name) !== strtolower($department)) {
            abort(403, 'Unauthorized department');
        }

        return $next($request);
    }
}
