<?php

use App\Helpers\{Arr, PostsTypes};

const FUNC_DEFINED = TRUE;

function vd($exit, ...$args){
	if (true) {
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
		echo '<div ',(isAdminSide() ? 'class="adminside"' : ''),'><small style="color: green;"><pre>',$trace['file'],':',$trace['line'],':</pre></small><pre>';
	}
	
	call_user_func_array(!$exit ? 'dump' : 'dd', $args[0] ? $args[0]: NULL);
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
	file_put_contents(cache_path(), $data);
}

function getCache(){
	if (file_exists(cache_path())) {
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
	// dd($post->_jmp_post_img);
	return isset($post->_jmp_post_img) ? uploads_url() . $post->_jmp_post_img : theme_url() . 'img/logo_trnsprnt1.jpg';
}


function theme_url(){
	return url('/') . '/themes/' . Options::get('theme') . '/';
}

function themeDir($side = null){
	if (!$side) {
		$side = (isAdminSide() ? 'admin' : 'front');
	} 
	
	return resource_path('views/') . Options::get('theme') . '/' . $side . '/';
}

function rootUri() {
	return '/';
}

function urlWithoutParams(){
	return url('/') . explode('?', $_SERVER['REQUEST_URI'])[0];
}

function viewWrap($templateFileName, $post, $args = null){
	if (isset($post['_jmp_post_template']) && $post['_jmp_post_template']) {
		$templateFileName = str_replace('.blade.php', '', $post['_jmp_post_template'], );
	} elseif (isset($post['post_type']) && file_exists($filename = themeDir() . $post['post_type'] . '.blade.php')) {
		$templateFileName = $post['post_type'];	
	}
	
	return view($templateFileName, $args ?? []);
}



function jmpHead($post = null){
	// global $post;
	$post = applyFilter('jhead', $post);
	if (!isset($post['title'])) {
		$post['title'] = 'no title';
	}
echo <<<EOT
<title>{$post['title']}</title>\n\t
EOT;
	doAction('jhead', $post);
}

function getMenu() {
	// $cacheFileName = 'menu/menu';
	// if(Common::getCache($cacheFileName, -1)) return;
	
	// $cats = DI::getD('db')->getAll('Select * from menu where menu_id = '.Common::getOption('menu_active_id').' ORDER BY sort, parent');
	// $cats = DB::table('menu')->where('menu_id', \Options::get('menu_active_id'))->orderBy('sort, parent');
	$cats = App\Admin\Menu::where('menu_id', \Options::get('menu_active_id'))->orderByRaw('sort, parent')->get();
	// dd($cats);
	if(!$cats) return;
	
	$newCats = array(
		'cats' => array(),
		'subCats' => array()
	);
	
	/*формируем из все категорий - главные категории и подкатегории*/
	foreach($cats as $cat){
		if($cat['parent'] == -1)
			$newCats['cats'][] = $cat;
		else
			$newCats['subCats'][$cat['parent']][] = $cat;
	}
	
	/*Очищаем изначальные категории, которые были в перемешку*/
	unset($cats);
	
	/*Начинаем выводить меню, первым пунктом статично поставим главную страницу*/
	?>
	<nav class="menu">
		<label for="mobile-nav"><div></div></label>
		<input type="checkbox" id="mobile-nav">
		<ul class="menu"><li><a href="<?=rootUri()?>">Главная</a></li>
	<?php
	/*Пройдемся по всем главнм категориям*/
	foreach($newCats['cats'] as $cat){
		$issetSubMenu = isset($newCats['subCats'][$cat['object_id']]);
		
		/*Проходим по подкатегориям, сохраняя их для вывода*/
		if($issetSubMenu){
			$subCatsView = '';
			foreach($newCats['subCats'][$cat['object_id']] as $subCat){
				$currentSubCatUrl = strpos($subCat['url'], 'http') === 0 ? $subCat['url'] : rootUri() . "{$subCat['url']}/";
				$subCatsView .= "<li><a href=\"{$currentSubCatUrl}\">{$subCat['name']}</a></li>";
			}
		}
		
		?>
		<li class="top-menu">
			<?php echo "<a href=\"".($issetSubMenu ? 'javascript:void(0);' : (strpos($cat['url'], 'http') === 0 ? $cat['url']:rootUri()."{$cat['url']}/"))."\">{$cat['name']}</a>";?>
			<?php if(!$issetSubMenu) {echo '</li>'; continue;}?>
			<ul class="submenu"><?=$subCatsView?></ul>
		</li>
		<?php
	}
	echo '
	<li class="top-menu hidd extra-contacts">
		<div>
			<a href="tel:+380677979385">+38 (067) 797-93-85</a>
			<a href="tel:+380632008595">+38 (063) 200-85-95</a>
			Почта: <a href="mailto:funkids@mail">funkidssodessa@gmail.com</a>
		</div>
	</li>
	</ul></nav>';
	
	// echo Common::setCache($cacheFileName);
}

function redir1($url, $code = 301){
	$codes = [
		301 => 'Moved Permanently',
		302 => 'Found',
	];
	
	doAction('before_redirect');
	
	header("HTTP/1.1 {$code} {$codes[$code]}");
	header('Location:' . $url);
	exit;
}

function rdr($url = ':back', $code = 301, $with = null, $messageKey = 'flash_errors') {
	if ($url == ':back') {
		$url = url()->previous();
	}
	
	if ($with) {
		\Session::flash($messageKey, is_array($with) ? $with : [$with]);
	}
	
	throw new \Illuminate\Http\Exceptions\HttpResponseException(redirect($url, $code));
}

function redirBack($with = null, $messageKey = 'flash_errors') {
	rdr(':back', 302, $with);
}

function redir($url, $code = 301, $with = null)
{
	rdr($url, $code, $with);
	// throw new \Illuminate\Http\Exception\HttpResponseException(redirect($to));
    // try {
        // \App::abort($code, '', ['Location' => $url]);
    // } catch (\Exception $exception) {
		// $codes = [
			// 301 => 'Moved Permanently',
			// 302 => 'Found',
		// ];
			// dd($exception);
			
		// doAction('before_redirect');
		
		// header("HTTP/1.1 {$code} {$codes[$code]}");
		// header('Location:' . $url);
		// exit;
        // the blade compiler catches exceptions and rethrows them
        // as ErrorExceptions :(
        //
        // also the __toString() magic method cannot throw exceptions
        // in that case also we need to manually call the exception
        // handler
        // $previousErrorHandler = set_exception_handler(function () {});
        // restore_error_handler();
        // call_user_func($previousErrorHandler, $exception);
        // die;
    // }
}

addAction('before_redirect', 'jump_beforeRedirect');
function jump_beforeRedirect() {
	Session::save();
}


addFilter('postTypeLink', 'myPostTypeLink');
function myPostTypeLink($link, $termsOnId, $termsOnParent, $postTerms)
{
	$replaceFormat = '/%.*%/';
	if (!preg_match($replaceFormat, $link)) {
		return $link;
	}
	
	if (!$postTerms) {
		$formatComponent = 'uncategorized';
	} elseif(is_string($postTerms)) {
		$formatComponent = $postTerms;
	} else {
		if (count($postTerms) == 1) {
			$formatComponent = $postTerms[0]->slug;
		} else {
			$postTermsOnId = Arr::itemsOnKeys($postTerms, ['id']);
			$current = $postTermsOnId[array_keys($postTermsOnId)[0]][0];
			$mergeKey = 'slug';
			$formatComponent = str_replace('|', '/', substr(Arr::builtHierarchyDown($termsOnId, $current, $mergeKey) 
			. '|' . 
			$current->$mergeKey . '|' . Arr::builtHierarchyUp($termsOnParent, $current, $postTermsOnId, $mergeKey), 1, -1));
		}
	}
// dd(preg_replace($replaceFormat, $formatComponent, $link));
// dd($replaceFormat, $formatComponent, $link);
	return preg_replace($replaceFormat, $formatComponent, $link);
}

function _q() {
	$bt = _backtrace();
	dd($bt);
}

function _backtrace() {
	return debug_backtrace(null, 3)[2];
}

function selectOne(string $query, array $args = null) {
	$result = !$args ? DB::select($query) : DB::select($query, $args);
	
	if ($result = $result[0] ?? null) {
		$result = is_array($result) ? $result : (array)$result;
		return array_shift($result);
	}
	
	return false;
}

function selectRow(string $query, array $args = null) {
	$result = !$args ? DB::select($query) : DB::select($query, $args);
	return (array)$result[0] ?? null;
}

function routeType($name) {
	return route(PostsTypes::get('type') . ".{$name}");
}

function textSanitize($content, $type = 'title', $tagsOn = false) {
	if ($content == '') return '';
	$types = [
		'all' => [
			'from' 	=> ['<?php', '<?', '<%', '?>'],
			'to' 	=> ['']
		],
		// 'content' => [
			// 'from' 	=> [],
			// 'to' 	=> []
		// ],
		'title' => [
			'from' 	=> ['\'', '"'],
			'to' 	=> ['’', '»']
		],
	];
	
	// if (!is_array($contents)) {
		// $contents = [$contents];
	// }
	
	// foreach ($contents as &$content) {
		// if (!is_string($content)) {
			// continue;
		// }
		
		$content = str_replace($types['all']['from'], $types['all']['to'], trim($content));
		$content = preg_replace('/\s+/', ' ', $content);
		
		if ($type && isset($types[$type])) {
			$content = str_replace($types[$type]['from'], $types[$type]['to'], $content);
		}
		
		if(!$tagsOn && !$type == 'content') {
			$content = htmlspecialchars($content);
		}
	// }
	
	return $content;
}

function clearCache($cacheFileName){
	if(is_file($cacheFileName = uploads_path() . 'cache/' . $cacheFileName . '.html')){
		unlink($cacheFileName);
	}
}

function expandDumpOnKeyDown() {
	?>
	<script>
		function $$_(callback){window.addEventListener('load', callback);}
		
		
		
		function $$$(){
			var list = document.getElementsByTagName('samp');
			
			return function () {
				// console.log(list[0].children);
				for (var i = 0; i < list.length; i++) {
					var children = list[i].children;
					for (var j = 0; j < children.length; j++) {
						if (children[j].className == 'sf-dump-ref sf-dump-toggle' || children[j].className == 'sf-dump-compact') {
							children[j].click();
						}
					}
				}
			}
			
		}
		
		function $$$__init() {
			$$_(() => {
				let a = $$$();
				// a();
				document.addEventListener('keydown', function(event) {
					if (event.code == 'KeyX' && (event.altKey)) {
						a();
					}
				});
			});
		}
		$$$__init();
	</script>
	<?php
}

function getExtraField($index, $name, $value){
	?>
	<div class="field mtop10">
		<div class="row">
			<div class="col-md-4">
				<input type="text" class="extra_name w100" value="<?=$name?>">
				<div class="mtop10">
					<input class="extra_field_delete" data-extra_index="<?=$index?>" type="button" value="Удалить">
					<input class="extra_field_update" data-extra_index="<?=$index?>" type="button" value="Обновить">
				</div>
			</div>
			<div class="col-md-8">
				<textarea name="extra_fileds[<?=$name?>]" class="w100" rows="2"><?=$value?></textarea>
			</div>
		</div>
	</div>
	<?php
}

function do_rmdir($dir) {
	if ($objs = glob($dir."/*")) {
		foreach ($objs as $obj) {
			is_dir($obj) ? do_rmdir($obj) : unlink($obj);
		}
	}
	
	rmdir($dir);
}
