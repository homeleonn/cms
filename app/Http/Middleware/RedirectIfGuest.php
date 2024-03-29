<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
			return abort(404);
		}

		return $next($request);
    }
}
