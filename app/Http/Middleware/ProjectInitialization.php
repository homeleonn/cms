<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Config;

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
		Config::optionsLoad();
        return $next($request);
    }
}