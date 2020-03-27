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

require dirname(__DIR__) . '/app/functions/posttypes.php';

Route::get('/', 'PostController@actionIndex')->name('index');

if (isAdminSide() || !isset($_SERVER['REQUEST_URI'])) {
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function(){
		// Route::resource('categories', 'CategoryController');
		// Route::resource('post', 'PostController');
		Route::get('/', 'PostController@actionDashboard')->name('admin.index')->middleware('web');
		
		Route::get('changeOrder/{orderType}', 'PostController@actionChangeOrder')->name('changeOrder');
		Route::post('changeOrderValue', 'PostController@actionChangeOrderValue')->name('changeOrderValue');
		
		Route::get('media/{async?}', 'MediaController@actionIndex')->name('media.index');
		Route::post('media/add/', 'MediaController@actionAdd')->name('media.add');
		Route::post('media/del/{id}', 'MediaController@actionDel')->name('media.del');
		
		Route::get('settings', 'SettingController@actionIndex')->name('settings.index');
		Route::post('settings/save', 'SettingController@actionSave')->name('settings.save');
		
		Route::match(['get', 'post'], 'menu', 'MenuController@actionIndex')->name('menu.index');
		Route::post('menu/edit', 'MenuController@actionEdit')->name('menu.index');
		Route::post('menu/select', 'MenuController@actionSelect')->name('menu.select');
		Route::post('menu/activate', 'MenuController@actionActivate')->name('menu.activate');
		
		Route::get('plugins', 'PluginController@actionIndex')->name('plugin.index');
		Route::get('plugins/settings/{pluginFoler}', 'PluginController@actionSettings')->name('plugin.index')->where('pluginFoler', '(.*)(/(.*))?');
		
		Route::post('user/clearcache/', 'SettingController@actionCacheClear')->name('cache.clear');
		
	});
	
}


Route::get('{slugMulti}', ['uses' => 'PostController@actionSingle'])->where('slugMulti', '[а-яА-ЯЁa-zA-Z0-9-\/]+');

