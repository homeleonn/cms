<?php

use App\Helpers\PostsTypes;

if (!function_exists('isAdminSide')) {
	function isAdminSide() {
		return isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/admin') === 0);
	}
}

function addPageType(string $type, array $options){
	PostsTypes::set($type, $options);
	
	if (!isAdminSide() || !isset($_SERVER['REQUEST_URI'])) {
		$pc = 'App\Http\Controllers\PostController';
		$sep = '/';
		$paged = $options['rewrite']['paged'] ? "{$sep}page/{page}" : '';
		
		if ($options['has_archive']) {
			Route::get($options['has_archive'] . '/{slug}', function($slug) use ($type, $pc){
				return App::make($pc)->run($type, 'actionSingle', [$slug]);
			});
		}
		
		if ($options['has_archive']) {
			Route::get($options['has_archive'] . $paged, function($page) use ($type, $pc){
				return App::make($pc)->run($type, 'actionList', [$page]);
			});
			Route::get($options['has_archive'], function() use ($type, $pc){
				return App::make($pc)->run($type, 'actionList', [1]);
			});
		}
		
		if (!empty($options['taxonomy'])) {
			if ($options['has_archive'] === false) $sep = '';
			foreach ($options['taxonomy'] as $t => $values) {
				Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", function($tslug) use ($type, $pc, $t){
					return App::make($pc)->run($type, 'actionList', [1, $t, $tslug]);
				});
				Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", function($tslug, $page) use ($type, $pc, $t){
					return App::make($pc)->run($type, 'actionList', [$page, $t, $tslug]);
				});
			}
		}
	}
	
	
	if (isAdminSide() || !isset($_SERVER['REQUEST_URI'])) {
		Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers\Admin', 'middleware' => 'web'], function() use ($type){
			Route::get($type, 'PostController@actionIndex')->name("{$type}.index");
			Route::post($type, 'PostController@actionStore')->middleware('web')->name("{$type}.store");
			Route::get($type.'/create', 'PostController@actionCreate')->name("{$type}.create");
			Route::put($type.'/{post}', 'PostController@actionUpdate')->name("{$type}.update");
			Route::delete($type.'/{post}', 'PostController@actionDestroy')->name("{$type}.destroy");
			Route::get($type.'/{post}/edit', 'PostController@actionEdit')->name("{$type}.edit");
			
			
			Route::get($type . '/term', 'PostController@actionTermIndex')->name("{$type}.term_index");
			Route::post($type . '/term', 'PostController@actionTermStore')->name("{$type}.term_store");
			Route::get($type . '/create-term', 'PostController@actionTermCreate')->name("{$type}.term_create");
			Route::put($type . '/term/{term}', 'PostController@actionTermUpdate')->name("{$type}.term_update");
			Route::delete($type . '/term/{term}', 'PostController@actionTermDestroy')->name("{$type}.term_destroy");
			Route::get($type . '/term/{term}/edit', 'PostController@actionTermEdit')->name("{$type}.term_edit");
		});
	}
}