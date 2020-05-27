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


//use Illuminate\Routing\Route;

//use Symfony\Component\Routing\Annotation\Route;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

DB::connection()->enableQueryLog();

Route::get('/', 'PostController@actionIndex')->name('index');
Route::get('user', 'UserController@index')->name('user.index');
Route::post('user/auth', 'UserController@auth')->name('user.auth');
Route::post('user/logout', 'UserController@logout')->name('user.logout');
Route::get('/{categorySlug}-c{categoryId}', 'PostController@actionCategory')->where(['categorySlug' => '([^/]*)', 'categoryId' => '(\d*)']);


if (isAdminSide() || !isset($_SERVER['REQUEST_URI'])) {
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function(){
		// Route::resource('categories', 'CategoryController');
		// Route::resource('post', 'PostController');
		Route::get('/', 'PostController@actionDashboard')->name('admin.index');
		
		Route::get('changeOrder/{orderType}', 'PostController@actionChangeOrder')->name('changeOrder');
		Route::post('changeOrderValue', 'PostController@actionChangeOrderValue')->name('changeOrderValue');
		
		Route::get('media/{async?}', 'MediaController@actionIndex')->name('media.index');
		Route::post('media/add/', 'MediaController@actionAdd')->name('media.add');
		Route::post('media/del/{id}', 'MediaController@actionDel')->name('media.del');
		
		Route::get('settings', 'SettingController@actionIndex')->name('settings.index');
		Route::post('settings/save', 'SettingController@actionSave')->name('settings.save');
		Route::get('settings/posttypes', 'SettingController@actionTypes')->name('admin.posttypes.index');
		Route::put('settings/posttypes/save', 'SettingController@actionTypesSave')->name('admin.posttypes.save');
		
		Route::match(['get', 'post'], 'menu', 'MenuController@actionIndex')->name('menu.index');
		Route::post('menu/edit', 'MenuController@actionEdit')->name('menu.index');
		Route::post('menu/select', 'MenuController@actionSelect')->name('menu.select');
		Route::post('menu/activate', 'MenuController@actionActivate')->name('menu.activate');
		
		Route::get('plugins', 'PluginController@actionIndex')->name('plugin.index');
		Route::get('plugins/settings/{pluginFoler}', 'PluginController@actionSettings')->name('plugin.index')->where('pluginFoler', '(.*)(/(.*))?');
		
		Route::post('user/clearcache/', 'SettingController@actionCacheClear')->name('cache.clear');
		Route::resource('category', 'TermController');
		Route::resource('tag', 'TermController');
	});
	
}



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::get('{slugMulti}', ['uses' => 'PostController@actionSingle'])->where('slugMulti', '[а-яА-ЯЁa-zA-Z0-9-\/]+');