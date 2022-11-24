<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictToUser
{
    /**
     * If the user is an admin, abort the request
     *
     * @param Request request The incoming request.
     * @param Closure next The next middleware to be called.
     *
     * @return The next middleware in the stack.
     */
    public function handle(Request $request, Closure $next)
    {
        $user_role = Auth::user()->role->name_slug;

        if (Auth::user()->is_admin) {
            abort(404, 'No encontrado');
        }
        return $next($request);
    }
}
