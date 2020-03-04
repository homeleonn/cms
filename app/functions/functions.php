<?php

use App\Helpers\PostsTypes;
use App\Helpers\Arr;

function vd($exit, ...$args){
	if (true) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
		echo '<small style="color: green;"><pre>',$trace['file'],':',$trace['line'],':</pre></small><pre>';
	}
	
	call_user_func_array(!$exit ? 'dump' : 'dd', $args[0] ? [$args[0]]: [NULL]);
}

function d(){
	vd(NULL, func_get_args());
}

function ddd(){
	vd(true, func_get_args());
}

function requestUri(){
	return $_SERVER['REQUEST_URI'];
}


if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

function arrayInCode($array, $arrayName = null, $level  = 0) {
	$tabs = str_repeat("\t", $level + 1);
	$code = '';
	$code .= (!$level ? ($arrayName ? '$' . $arrayName . ' = ' : 'return ') . '[' : "[\n");
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			if ($level < 1) $code .= "\n";
			$code .= "{$tabs}'{$key}' => ";
			$code .= arrayInCode($value, $arrayName, $level + 1);
		} else {
			if ($level == 0) $code .= "\n";
			$code .= "{$tabs}'{$key}' => '{$value}',\n";
		}
	}
	if ($level > 1) $code .= str_repeat("\t", $level - 1);
	$code .= $level ? "\t]" : "]";
	$code .= !$level ?  ';' : ',';if ($level > 1) $code .= "\n";
	
	return $code;
}

addPageType('post', [
		'type' => 'post',
		'title' => 'Блог',
		'h1' => 'Блог',
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
		'rewrite' => ['slug' =>'', 'with_front' => true, 'paged' => false],
]);


addPageType('test', [
		'title' => 'Test',
		'description' => 'Test Description',
		'h1' => 'Test',
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
		'h1' => 'Программы',
		'hierarchical' => false,
		'has_archive'  => 'programs',
		'rewrite' => ['slug' => 'programs', 'with_front' => false, 'paged' => 20],
		// 'taxonomy' => [
			// 'age' => [
				// 'title' => 'Возрастная категория',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
			// 'gen' => [
				// 'title' => 'Пол ребенка',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
		// ]
]);

addPageType('service', [
		'type' => 'service',
		'title' => 'Доп. услуги',
		'_seo_title' => 'Дополнительные услуги | Funkids',
		'h1' => 'Дополнительные услуги',
		'title_for_admin' => 'Доп. услуги',
		'description' => 'Дополнительные услуги на детский праздник, мыльные пузыри, сладкая вата, всё что бы разнообразить праздничный день, запоминающиеся мгновения жизни ребенка | FunKids',
		'add' => 'Добавить услугу',
		'edit' => 'Редактировать услугу',
		'delete' => 'Удалить услугу',
		'common' => 'услуг',
		'hierarchical' => false,
		'has_archive'  => 'services',
		'rewrite' => ['slug' => 'services', 'with_front' => false, 'paged' => 20],
]);


function getRawOptions(){
	// return
}

function isMain(){
	return url('/') == url()->current();
}

function plugins(array $activePlugins = []):array
{
	static $activated = false;
	
	$pluginsRootFolder = public_path() . '/plugins/';
	$pluginFolders = glob($pluginsRootFolder . '*');
	
	if(!$pluginFolders) return false;
	
	$plugins = [];
	foreach($pluginFolders as $folder)
	{
		$basename = basename($folder);
		$mainFile = $folder . '/' . $basename . '.php';
		if(file_exists($mainFile))
		{
			$pluginPath = str_replace($pluginsRootFolder, '', $mainFile);
			$isActive   = in_array($pluginPath, $activePlugins);
			
			if(!$activated && $isActive){
				include $mainFile;
			}
			
			$plugins[] = ['src' => $mainFile, 'active' => $isActive, 'path' => $pluginPath];
		}
	}
	
	$activated = true;
	return $plugins;
}

// function addPageType(string $type, array $options){
	// PostsTypes::set($type, $options);
	// $sep = '/';
	// $paged = $options['rewrite']['paged'] ? "{$sep}page/{page}" : '';
	
	// if ($options['has_archive'])
		// Route::get($options['has_archive'] . '/{slug}', ['uses' => 'PostController@actionSingle', 'type' => $type, 'args' => ['slug']]);
	
	// if ($options['has_archive']) {
		// Route::get($options['has_archive'] . $paged, ['uses' => 'PostController@actionList', 'type' => $type, 'args' => ['page']]);
		// Route::get($options['has_archive'], ['uses' => 'PostController@actionList', 'type' => $type]);
	// }
	
	// if (!empty($options['taxonomy'])) {
		// if ($options['has_archive'] === false) $sep = '';
		// foreach ($options['taxonomy'] as $t => $values) {
			// Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", ['uses' => 'PostController@actionList', 'type' => $type, 'taxonomy' => $t, 'args' => ['tslug']]);
			// Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", ['uses' => 'PostController@actionList', 'type' => $type, 'taxonomy' => $t, 'args' => ['tslug', 'page']]);
		// }
	// }
// }

// function addPageType(string $type, array $options){
	// PostsTypes::set($type, $options);
	// $sep = '/';
	// $paged = $options['rewrite']['paged'] ? "{$sep}page/{page}" : '';
	
	// if ($options['has_archive'])
		// Route::get($options['has_archive'] . '/{slug}', ['uses' => 'PostController@actionSingle', 'type' => $type, 'args' => ['slug']]);
	
	// if ($options['has_archive']) {
		// Route::get($options['has_archive'] . $paged, ['uses' => 'PostController@actionList', 'type' => $type, 'args' => ['page']]);
		// Route::get($options['has_archive'], ['uses' => 'PostController@actionList', 'type' => $type]);
	// }
	
	// if (!empty($options['taxonomy'])) {
		// if ($options['has_archive'] === false) $sep = '';
		// foreach ($options['taxonomy'] as $t => $values) {
			// Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", ['uses' => 'PostController@actionList', 'type' => $type, 'taxonomy' => $t, 'args' => ['tslug']]);
			// Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", ['uses' => 'PostController@actionList', 'type' => $type, 'taxonomy' => $t, 'args' => ['tslug', 'page']]);
		// }
	// }
// }

// function addPageType(string $type, array $options){
	// PostsTypes::set($type, $options);
	// $sep = '/';
	// $paged = $options['rewrite']['paged'] ? "{$sep}page/{page}" : '';
	
	// if ($options['has_archive']) {
		// Route::get($options['has_archive'] . '/{slug}', ['uses' => 'PostController@actionSingle__' . $type]);
	// }
	
	// if ($options['has_archive']) {
		// Route::get($options['has_archive'] . $paged, ['uses' => 'PostController@actionList__' . $type]);
		// Route::get($options['has_archive'], ['uses' => 'PostController@actionList__' . $type]);
	// }
	
	// if (!empty($options['taxonomy'])) {
		// if ($options['has_archive'] === false) $sep = '';
		// foreach ($options['taxonomy'] as $t => $values) {
			// Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", ['uses' => "PostController@actionList__{$type}00{$t}"]);
			// Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", ['uses' => "PostController@actionList__{$type}00{$t}"]);
		// }
	// }
// }



function addPageType(string $type, array $options){
	PostsTypes::set($type, $options);
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
			Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", function($tslug) use ($type, $pc){
				return App::make($pc)->run($type, 'actionList', [1, $tslug, $t]);
			});
			Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", function($tslug, $page) use ($type, $pc){
				return App::make($pc)->run($type, 'actionList', [$page, $tslug, $t]);
			});
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
	return url('/') . '/themes/' . Options::get('theme') . '/';
}

function urlWithoutParams(){
	return url('/') . explode('?', $_SERVER['REQUEST_URI'])[0];
}

function viewWrap($templateFileName, $post, $args = null){
	if (isset($post['_jmp_post_template']) && $post['_jmp_post_template']) {
		$templateFileName = $post['_jmp_post_template'];
		// $templateFileName = strpos($post['_jmp_post_template'], '.php') === false ? $post['_jmp_post_template'] : substr($post['_jmp_post_template'], 0, -4);
	}
		
	return $args ? view(Options::get('theme') . '.' . $templateFileName, $args) 
				 : view(Options::get('theme') . '.' . $templateFileName) ;
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