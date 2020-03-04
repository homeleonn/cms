<?php
//var_dump($_SERVER);exit;
// ini_set('xdebug.var_display_max_depth', 50);
// ini_set('xdebug.var_display_max_children', 256);
// ini_set('xdebug.var_display_max_data', 1024);
ini_set('date.timezone', 'Europe/Kiev');
ini_set('xdebug.overload_var_dump', '1');
const URL_PATTERN 		= '[а-яА-ЯЁa-zA-Z0-9-]+';
const URL_PATTERN_SLASH = '[а-яА-ЯЁa-zA-Z0-9-\/]+';
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
d(microtime(true) - LARAVEL_START);
if ($t = DB::getQueryLog()) 
	dump($t);
	// dd(array_map(function($tt){
		// if (isset($tt[0])) {
			// foreach ($tt as $key => $t) {
				// dump($t);
				// $tt[$key]['time'] = (float)$t['time'] / 1000;
			// }
		// } else {
			// $tt['time'] = (float)$tt['time'] / 1000;
		// }
		// return $tt;
		
	// }, $t));