<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


DB::connection()->enableQueryLog();

require dirname(__DIR__) . '/app/functions/functions.php';


Route::get('/', 'PostController@actionIndex')->name('index');

if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') === 0 || !isset($_SERVER['REQUEST_URI'])) {
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function(){
		// Route::resource('categories', 'CategoryController');
		Route::resource('posts', 'PostController');
		Route::get('/', 'PostController@actionIndex1');
	});
}


Route::get('{slugMulti}', ['uses' => 'PostController@actionSingle'])->where('slugMulti', '[а-яА-ЯЁa-zA-Z0-9-\/]+');

