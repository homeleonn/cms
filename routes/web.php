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

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function(){
	Route::resource('categories', 'CategoryController');
	Route::resource('posts', 'PostController');
});

// Route::get('foo', function(){
    // return App::make('App\Http\Controllers\PostController')->foo(1, 2);
// });

Route::get('{slugMulti}', ['uses' => 'PostController@actionSingle', 'foo' => 'bar'])->where('slugMulti', '[а-яА-ЯЁa-zA-Z0-9-\/]+');


// Route::get('foo', function(){
    // return App::make('App\Http\Controllers\PostController')->foo(1, 2);
// });

// Route::get('{slugMulti}', function($slug){
	// return App::make('App\Http\Controllers\PostController')->run('page', 'actionSingle', [$slug]);
// })->where('slugMulti', '[а-яА-ЯЁa-zA-Z0-9-\/]+');
