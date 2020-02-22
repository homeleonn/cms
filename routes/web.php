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
// dd(dirname(__DIR__));
Route::get('/', function(Request $request){
	return view('welcome');
})->name('index');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function(){
	Route::resource('categories', 'CategoryController');
	Route::resource('posts', 'PostController');
});