<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!$request->user()) {
            return redirect('/login');
        }

        $userRole = $request->user()->role;
        // Roles configuration: 'superadmin', 'admin', 'staff'

        if (!in_array($userRole, $roles)) {
            abort(403, 'Anda tidak memiliki hak akses (Unauthorized) untuk masuk ke halaman ini.');
        }

        return $next($request);
    }
}
