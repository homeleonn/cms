<?php

use App\Helpers\PostsTypes;
use App\Helpers\Arr;
use App\Helpers\Config;

function vd($exit, ...$args){
	$trace = debug_backtrace()[1];
	echo '<small style="color: green;"><pre>',$trace['file'],':',$trace['line'],':</pre></small><pre>';
	call_user_func_array(!$exit ? 'dump' : 'dd', $args[0] ? [$args[0]]: [NULL]);
}

function d(){
	vd(NULL, func_get_args());
}

function ddd(){
	vd(true, func_get_args());
	// requestStats();
	// exit;
}

addPageType('post', [
		'type' => 'post',
		'title' => 'Блог',
		'title_for_admin' => 'Записи',
		'description' => 'Блог',
		'add' => 'Добавить запись',
		'edit' => 'Редактировать запись',
		'delete' => 'Удалить запись',
		'common' => 'записей',
		'hierarchical' => false,
		'has_archive'  => 'blog',
		'taxonomy' => [
			'category' => [
				'title' => 'Категория',
				'add' => 'Добавить категорию',
				'edit' => 'Редактировать категорию',
				'delete' => 'Удалить категорию',
				'hierarchical' => false,
			],
		],
		'rewrite' => ['slug' => 'blog/%category%', 'with_front' => false, 'paged' => 20],
]);



addPageType('page', [
		'type' => 'page',
		'title' => '',
		'title_for_admin' => 'Страницы',
		'description' => 'Страницы',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
		'hierarchical' => true,
		'has_archive'  => false,
		'taxonomy' => [],
		'rewrite' => ['with_front' => true, 'paged' => false],
]);


addPageType('test', [
		'title' => 'Test',
		'description' => 'Test Description',
		'hierarchical' => false,
		'has_archive'  => 'tests',
		'rewrite' => ['slug' => 'tests', 'with_front' => false, 'paged' => 20],
		'taxonomy' => [
			'newscat' => [
				'title' => 'Возрастная категория',
				'add' => 'Добавить возрастную категорию',
				'edit' => 'Редактировать возрастную категорию',
				'delete' => 'Удалить возрастную категорию',
				'hierarchical' => true,
			],
		]
]);

addPageType('program', [
		'title' => 'Программы',
		'description' => 'Programs Description',
		'hierarchical' => false,
		'has_archive'  => 'programs',
		'rewrite' => ['slug' => 'programs', 'with_front' => false, 'paged' => 20],
		'taxonomy' => [
			'age' => [
				'title' => 'Возрастная категория',
				'add' => 'Добавить возрастную категорию',
				'edit' => 'Редактировать возрастную категорию',
				'delete' => 'Удалить возрастную категорию',
				'hierarchical' => true,
			],
			'gen' => [
				'title' => 'Пол ребенка',
				'add' => 'Добавить возрастную категорию',
				'edit' => 'Редактировать возрастную категорию',
				'delete' => 'Удалить возрастную категорию',
				'hierarchical' => true,
			],
		]
]);

function addPageType(string $type, array $options){
	PostsTypes::set($type, $options);
	$sep = '/';
	$paged = $options['rewrite']['paged'] ? "{$sep}page/{page}" : '';
	
	if ($options['has_archive'])
		Route::get($options['has_archive'] . '/{slug}', ['uses' => 'PostController@actionSingle', 'type' => $type, 'args' => ['slug']]);
	
	if ($options['has_archive']) {
		Route::get($options['has_archive'] . $paged, ['uses' => 'PostController@actionList', 'type' => $type, 'args' => ['page']]);
		Route::get($options['has_archive'], ['uses' => 'PostController@actionList', 'type' => $type]);
	}
	
	if (!empty($options['taxonomy'])) {
		if ($options['has_archive'] === false) $sep = '';
		foreach ($options['taxonomy'] as $t => $values) {
			Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", ['uses' => 'PostController@actionList', 'type' => $type, 'taxonomy' => $t, 'args' => ['tslug']]);
			Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", ['uses' => 'PostController@actionList', 'type' => $type, 'taxonomy' => $t, 'args' => ['tslug', 'page']]);
		}
	}
}

function uploads_url(){
	return url('/') . '/uploads/';
}

function add($type, $funcName, $userFunc, $front = false){
	// if(is_array($userFunc)){
		// if(isset($userFunc[0]) && isset($userFunc[1])){
			// if(is_object($userFunc[0]) && method_exists($userFunc[0], $userFunc[1])){
				// $userFunc[0]->{$userFunc[1]}();
			// }
		// }
		// dd($userFunc);
	// }
	if($front){
		if(!isset($GLOBALS['jump_'.$type][$funcName]))
			$GLOBALS['jump_'.$type][$funcName] = [];
		
		array_unshift($GLOBALS['jump_'.$type][$funcName], $userFunc);
	}else{
		$GLOBALS['jump_'.$type][$funcName][] = $userFunc;
	}
	
}

function addAction($actionName, $userFunc, $front = false){
	add('actions', $actionName, $userFunc, $front);
}

function addFilter($filterName, $userFunc){
	add('filters', $filterName, $userFunc);
}

function apply(){
	$args = func_get_args();
	if(empty($args)) 
		return;
	
	$type = 'jump_' . array_shift($args);
	if(!count($args)) return;
	$funcName = array_shift($args);
	
	if(!isset($GLOBALS[$type][$funcName]))
		return isset($args[0]) ? $args[0] : false;
	
	$isfilters = $type == 'jump_filters';
	foreach($GLOBALS[$type][$funcName] as $key => $filter){
		$result = call_user_func_array($filter, $args);
		if($isfilters){
			$args[0] = $result;
		}
	}
	
	return $args[0] ?? false;
}

function doAction(){
	call_user_func_array('apply', array_merge(['actions'], func_get_args()));
}
function applyFilter(){
	return call_user_func_array('apply', array_merge(['filters'], func_get_args()));
}

function postImgSrc($post, $thumbnail = 'orig'){
	$validKeys = ['thumbnail', 'medium'];
	
	if(in_array($thumbnail, $validKeys) && isset($post->_jmp_post_img_meta['sizes'][$thumbnail])){
		return uploads_url() . substr($post->_jmp_post_img, 0, strrpos($post->_jmp_post_img, '/') + 1) . $post->_jmp_post_img_meta['sizes'][$thumbnail]['file'];
	}
	
	return isset($post->_jmp_post_img) ? uploads_url() . $post->_jmp_post_img : theme_url() . 'img/logo_trnsprnt1.jpg';
}


function theme_url(){
	return url('/') . '/themes/' . Config::get('theme') . '/';
}

function urlWithoutParams(){
	return url('/') . explode('?', $_SERVER['REQUEST_URI'])[0];
}



addFilter('postTypeLink', 'myPostTypeLink');
function myPostTypeLink($link, $termsOnId, $termsOnParent, $postTerms){//dd(func_get_args());
	$replaceFormat = '/%.*%/';
	if(!preg_match($replaceFormat, $link)) return $link;
	if(!$postTerms){
		$formatComponent = 'uncategorized';
	}elseif(is_string($postTerms)){
		$formatComponent = $postTerms;
	}else{
		$postTermsOnId = Arr::itemsOnKeys($postTerms, ['id']);
		$current = $postTermsOnId[array_keys($postTermsOnId)[0]][0];
		$mergeKey = 'slug';
		// $formatComponent = str_replace('|', '/', substr(Arr::builtHierarchyDown($termsOnId, $current, $mergeKey) . '|' . $current[$mergeKey] . '|' . Arr::builtHierarchyUp($termsOnParent, $current, $postTermsOnId, $mergeKey), 1, -1));
		$formatComponent = '---';
	}
	return preg_replace($replaceFormat, $formatComponent, $link);
}