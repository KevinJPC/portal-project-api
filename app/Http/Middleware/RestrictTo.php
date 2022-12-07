<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictTo
{
    /**
     * If the user's role is different from the one passed by parameter, it aborts the request with a 403 status code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $role = $role === 'admin';
        $role_user = Auth::user()->is_admin;

        if ($role !== $role_user) {
            abort(403, 'Accesso prohibido');
        }
        return $next($request);
    }
}
