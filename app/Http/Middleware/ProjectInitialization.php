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
		Config::set('controller', 'App\Http\Controllers\PostController');
		plugins(unserialize(Config::get('plugins_activated')));
		require public_path() . '/themes/' . Config::get('theme') . '/functions.php';
		
		// \View::addLocation(resource_path() . '/views/' . Config::get('theme'));
		// dd(\App::make('view'), resource_path());
		// dd(get_class_methods(\App::make('view')));
        $response = $next($request);
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
