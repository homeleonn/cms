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
		if ($cache = getCache()) {
			return response($cache);
		}
		
		$response = $next($request);
		
		setCache($response->getContent());
		
		return $response;
    }
}
