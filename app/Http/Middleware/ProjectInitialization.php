<?php

namespace App\Http\Middleware;

use Closure;

class ProjectInitialization
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
		
		if (!isAdminSide() && \Options::get('cache_enable')) {
			if ($cache = getCache(null, -1, false)) {
				return response($cache);
			}
			
			$response = $next($request);
			
			setCache(null, $response->getContent());
		} else {
			$response = $next($request);
		}
		
		return $response;
    }
}
