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
		// dd(Options::get('plugins_activated'));
		// Options::save();
		// Options::delete('description');exit;
		// setOption('foo', 'bar');
		plugins(Options::get('plugins_activated', true));
		require public_path() . '/themes/' . Options::get('theme') . '/functions.php';
		
		// \View::addLocation(resource_path() . '/views/' . Options::get('theme'));
		// dd(\App::make('view'), resource_path());
		// dd(get_class_methods(\App::make('view')));
		// ob_start('a');
		// dd($request, requestUri());
		// setCache();
        $response = $next($request);
		
		// dd($response);
		// ob_flush();
		// $a = ob_get_contents();
		// dd($a);
		
		// dd(get_class_methods($response));
		// dd($response, \App::make('view'));
		// $post = $response->getOriginalContent()->getData()[0]['post'];
		// if (isset($post['_jmp_post_template']) && $post['_jmp_post_template']) {
			// $templateFileName = $post['_jmp_post_template'];
		// }
		// ddd($response->getOriginalContent()->getData()[0]['post']);
		
		return $response;
    }
}
