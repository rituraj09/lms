<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {

            if (!Auth::guard($guard)->check()) {
                if ($guard == 'admin') {
                    return redirect()->route('admin.login');
                }
            }
        }
        return $next($request);
    }
}
