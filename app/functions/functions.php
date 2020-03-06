<?php

use App\Helpers\PostsTypes;
use App\Helpers\Arr;

require 'posttypes.php';

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

function cache_path(){
	return uploads_path() . '/cache/front/' . md5(requestUri()) . '.html';
}

function setCache($data){
	if (Options::get('cache_enable')) {
		file_put_contents(cache_path(), $data);
	}
}

function getCache(){
	if (Options::get('cache_enable') && file_exists(cache_path())) {
		return file_get_contents(cache_path());
	}
	return false;
}


if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}




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
			Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", function($tslug) use ($type, $pc, $t){
				return App::make($pc)->run($type, 'actionList', [1, $tslug, $t]);
			});
			Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", function($tslug, $page) use ($type, $pc, $t){
				return App::make($pc)->run($type, 'actionList', [$page, $tslug, $t]);
			});
		}
	}
}

function uploads_url(){
	return url('/') . '/uploads/';
}

function uploads_path(){
	return public_path() . '/uploads/';
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
	
	return view(Options::get('theme') . '.' . $templateFileName, $args ?? []);
}

function redir($url = NULL, $code = 301){
		$codes = [
			301 => 'Moved Permanently',
		];
			
		header("HTTP/1.1 {$code} {$codes[$code]}");
		header('Location:' . $url);
		exit;
	}



addFilter('postTypeLink', 'myPostTypeLink');
function myPostTypeLink($link, $termsOnId, $termsOnParent, $postTerms){//dd(func_get_args());
	$replaceFormat = '/%.*%/';
	if(!preg_match($replaceFormat, $link)) return $link;
	// dd($postTerms, Arr::itemsOnKeys($postTerms, ['id']));
	if(!$postTerms){
		$formatComponent = 'uncategorized';
	}elseif(is_string($postTerms)){
		$formatComponent = $postTerms;
	}else{
		if (count($postTerms) == 1) {
			$formatComponent = $postTerms[0]->slug;
		} else {
			$postTermsOnId = Arr::itemsOnKeys($postTerms, ['id']);
			$current = $postTermsOnId[array_keys($postTermsOnId)[0]][0];
			$mergeKey = 'slug';
			$formatComponent = str_replace('|', '/', substr(Arr::builtHierarchyDown($termsOnId, $current, $mergeKey) . '|' . $current[$mergeKey] . '|' . Arr::builtHierarchyUp($termsOnParent, $current, $postTermsOnId, $mergeKey), 1, -1));
			// $formatComponent = '---';
		}
	}
	return preg_replace($replaceFormat, $formatComponent, $link);
}