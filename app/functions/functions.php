<?php

use App\Helpers\Arr;

const FUNC_DEFINED = TRUE;

function vd($exit, ...$args){
	if (true) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
		echo '<div ',(isAdminSide() ? 'class="adminside"' : ''),'><small style="color: green;"><pre>',$trace['file'],':',$trace['line'],':</pre></small><pre>';
	}
	
	call_user_func_array(!$exit ? 'dump' : 'dd', $args[0] ? [$args[0]]: [NULL]);
	echo '</pre></div>';
}

function d(){
	vd(NULL, func_get_args());
}

function ddd(){
	vd(true, func_get_args());
}

function isAdminSide(){
	return isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/admin') === 0);
}

function requestUri(){
	return $_SERVER['REQUEST_URI'];
}

function cache_dir(){
	return uploads_path() . '/cache/front/';
}

function cache_path(){
	return cache_dir() . md5(requestUri()) . '.html';
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

class _Cache
{
	public static function getCache($cacheFileName, $delay = 86400, $outNow = true){
		if (!Options::get('cache_enable')) return false;
		$cacheFileName = cache_dir() . $cacheFileName . '.html';
		
		if (file_exists($cacheFileName))
		{
			if ($delay == -1 || (filemtime($cacheFileName) > time() - $delay)) {
				if (($data = file_get_contents($cacheFileName)) === FALSE) return false;
				
				if ($data != '') {
					if ($outNow) {
						echo $data;
					} else {
						return $data;
					}   
					return true;
				}
			}
		}
		ob_start();
		return false;
	}

	public static function setCache($cacheFileName, $data = false){
		// if (!$data) $data = ob_get_clean();
		// if (!Options::get('cache_enable')) return $data;
		if (!Options::get('cache_enable')) return false;
		$data = ob_get_clean();
		$cacheFileName = cache_dir() . $cacheFileName . '.html';
		
		if (!is_dir($dir = dirname($cacheFileName))) {
			mkdir($dir, 0755, true);
		}
		
		file_put_contents($cacheFileName, $data, LOCK_EX);
		return $data;
	}
}


if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
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




function uploads_url(){
	return url('/') . '/uploads/';
}

function uploads_path(){
	return public_path() . '/uploads/';
}

function add($type, $funcName, $userFunc, $front = false){
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

function themeDir(){
	return resource_path('views/') . Options::get('theme') . '/';
}



function urlWithoutParams(){
	return url('/') . explode('?', $_SERVER['REQUEST_URI'])[0];
}

function viewWrap($templateFileName, $post, $args = null){
	if (isset($post['_jmp_post_template']) && $post['_jmp_post_template']) {
		$templateFileName = $post['_jmp_post_template'];
	}
	
	return view($templateFileName, $args ?? []);
}

function redir($url = NULL, $code = 301){
	$codes = [
		301 => 'Moved Permanently',
	];
		
	header("HTTP/1.1 {$code} {$codes[$code]}");
	header('Location:' . $url);
	exit;
}


function funkids_readyToHolyday(){
?>
<div class="holyday" id="holyday">
	<h2 class="section-title">Готовимся к празднику уже сейчас</h2>
	<div id="order-question">
		Напишите нам и наш менеджер ответит на все Ваши вопросы
		<div class="inp1">
			<input type="text" id="qname" name="name" placeholder="Имя*">
			<input type="text" id="qtel" name="tel" placeholder="Телефон*">
			<input type="text" id="qmail" name="email" placeholder="Электронная почта">
		</div>
		<div class="captcha-wrapper none center">
			<img alt="captcha" class="captcha pointer captcha-reload">
			<span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span><br>Введите символы с картинки 
			<input type="text" class="captcha-code">
		</div>
		<input type="button" class="button1" id="q-set" value="Отправить">
	</div>
</div>
<?php
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