<?php

namespace App\Http\Middleware;

use Closure, Options;

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
		Options::set('controller', 'App\Http\Controllers\PostController');
		// Options::save('foo', 'bar');
		// Options::delete('foo');
		// dd(Options::getAll());
		plugins(Options::get('plugins_activated', true));
		require public_path() . '/themes/' . Options::get('theme') . '/functions.php';
		
		if ($cache = getCache()) {
			return response($cache);
		}
		
		$response = $next($request);
		
		setCache($response->getContent());
		
		return $response;
    }
}
