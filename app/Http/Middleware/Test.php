<?php

namespace App\Http\Middleware;

use Closure;

class Test
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
        $response = $next($request);
		
		// if (file_exists(public_path($request->path() . '.php'))) {
			// echo view(join('.', $request->segments()));
		// }
		// dump($response, $request, $request->path(), $request->fullUrl(), $request->decodedPath(), $request->segments(), join($request->segments()), \Storage::disk('public')->exists('test.php'), file_exists(public_path('test.php')));

		return $response;
    }
}
