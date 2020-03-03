<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\{Post, Taxonomy};
use App\Helpers\{PostsTypes, Arr, Pagination, Config};
use DB;

class PostController extends Controller
{
	private $img = '_jmp_post_img';
	private $launched = false;
	
	public function run($type, $method = null, $args = null)
	// public function run($type)
	{
		// dd(app()->get('request')->route()->getAction());
		if ($this->launched) return;
		$this->launched = true;
		$this->model = new Post;
		$this->model->taxonomy = new Taxonomy();
		PostsTypes::setCurrentType($type);
		$this->postOptions = $this->post = PostsTypes::getCurrent();
		$this->breadcrumbs = [];
		
		if ($method) {
			array_unshift($args, app()->get('request'));
			return $this->$method(...$args);
		}
	}
	
    public function actionIndex(Request $request)
	{
		// dd(__METHOD__);
		// funKids_all();
        // extract($this->prepareArgs($request->route()->getAction(), func_get_args()) ?? []);
		$this->run('page');
		$frontPage = Config::get('front_page');
		if (is_numeric($frontPage)) {
			return $this->actionSingle($request, NULL, $frontPage);
		} else {
			return $this->last();
		}
    }
	
	public function actionSingle(Request $request, $slug, $id = null)
	{
		$this->run('page');
		// extract($this->prepareArgs($request->route()->getAction(), func_get_args()) ?? []);
		// dd(__METHOD__, get_defined_vars());
		// dd($this);
		// dd($this->postOptions);
		if ($id) {
			if (!$post = Post::find($id)) {
				abort(404);
			} else {
				$post->getMeta(true);
				return viewWrap('single', $post)->with('post', $post);
			}
		} else {
			$hierarchy 	= explode('/', $slug);
			$slug 		= array_pop($hierarchy); // get last url part
			
			if (!$post = Post::where('slug', $slug)->first()) {
				abort(404);
			}
			$post->getMeta(true);
			
			// If this post is the front
			if ($result = $this->checkFrontPageAliase($post['id'])) {
				if (is_bool($result)) {
					return viewWrap('single', $post)->with('post', $post);
				} else {
					return $result;
				}
			}
			
			// If type of this post related taxonomy
			if (!$post->postOptions['hierarchical']) {
				$post = $this->taxonomyPost($post);
			} else { // If type of this post is hierarchical structure, check hierarchy
				if (!empty($hierarchy)) {
					if ($redirectResponce = $this->checkHierarchy($post['slug'], $post['parent'], $hierarchy)) {
						return $redirectResponce;
					}
				}
			}
		}
		
		$this->createBreadCrumbs($post, NULL, $hierarchy, $post['terms']);
		// dd($post);
		// return view('single')->with('post', $post);
		return viewWrap('single', $post)->with('post', $post);
	}
	
	private function checkFrontPageAliase($postId)
	{
		if ($postId == Config::get('front_page')) {
			if(url('/') != urlWithoutParams()) {
				return redirect('/', 301);
			}
			return true;
		}
		return false;
	}
	
	private function checkHierarchyCorrect($slug, $hierarchy)
	{
		if ($slug && count($hierarchy) > 1) {
			if (in_array('', $hierarchy)) {
				abort(404);
			}
		}
	}
	
	private function checkHierarchy($slug, $parent, $hierarchy){//dd(func_get_args());
		if (!$parent) {
			if ($hierarchy) {
				abort(404);
			}
			return false;
		} else {
			if (!$hierarchy) {
				// взять все страницы, создать иерархию и перенаправить
				//dd($this->model->getPostsByPostType('page') , $parent, 'slug');
				return redirect(url('/') . $this->getParentHierarchy($parent, $this->model->getByType('page'),  'slug') . '/' . $slug, 301);
			} else {
				$parents = DB::table('posts')
							->select('id', 'title', 'short_title', 'slug', 'parent')
							->whereIn('slug', $hierarchy)
							->orderBy('parent', 'desc')
							->get();
				// ddd($parents, $hierarchy, $parent);
				if (count($parents) < count($hierarchy)) {
					abort(404);
				} else {
					$h = array_reverse($hierarchy);
					$tempParent = $parent;
					$i = 0;
					$addBreadCrumbs = [];
					
					foreach ($parents as $parent) {//d($parent->id);
						if ($parent->id != $tempParent || $parent->slug != $h[$i]) {
							abort(404);
						}
						$tempParent = $parent->parent;
						$addBreadCrumbs[$h[$i]] = $parent->short_title ?: $parent->title;
						$i++;
					}
					
					if ($tempParent) {
						abort(404);
					}
					
					foreach(array_reverse($addBreadCrumbs) as $link => $title){
						$this->breadcrumbs[($link)] = $title;
					}
				}
			}
		}
		
		return false;
	}
	
	private function getParentHierarchy($parentId, $items, $compare)
	{
		foreach($items as $item){
			$itemsOnId[$item->id] = $item;
		}
		$hierarchy = $this->setHierarchy($itemsOnId, $parentId, $compare);
		$hierarchy = implode('/', array_reverse(explode('|', substr($hierarchy, 0, -1))));
		return $hierarchy;
	}
	
	private function setHierarchy($items, $parentId, $compare)
	{
		if (!isset($items[$parentId])) {
			return false;
		}
		$hierarchy = $items[$parentId][$compare] . '|';
		if (isset($items[$parentId]['parent']) && $items[$parentId]['parent']) {
			$hierarchy .= $this->setHierarchy($items, $items[$parentId]['parent'], $compare);
		}
		return $hierarchy;
	}
	
	/**
	 *  Запись связанная таксономией
	 *  Строит правильную ссылку, опираясь на пренадлежность к терминам и проверяем с пришедшей
	 *  Строит html список терминов, к котором пренадлежит запись
	 *  
	 *  @param type $post
	 *  
	 *  @return post
	 */
	private function taxonomyPost($post)
	{
		if (empty($this->postOptions['taxonomy'])) {
			$termsOnId = $termsOnParent = $postTerms = [];
			
			$post['terms'] = NULL;
		} else {
			// Get terms by post taxonomies
			$terms = $this->model->taxonomy->getByTaxonomies(); 
			
			// Получим термины относящиеся к данной записи, которые привязаны к таксономиям данного типа записи
			$postTerms = $this->model->getPostTerms($post['id'], array_keys($this->postOptions['taxonomy']));
			
			// dd($terms, $postTerms);
			// $postTerms = $this->model->getPostTerms(' and tr.object_id = ' . $post['id'] . ' and tt.taxonomy IN(\''.implode("','", $this->postOptions['taxonomy']).'\')');
			
			// Сгрупируем все термины данных таксономий по айди и родителю
			list($termsOnId, $termsOnParent) = Arr::itemsOnKeys($terms, ['id', 'parent']);
			
			// Получим термины в виде списка html
			// $post['terms'] = Common::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $postTerms), Options::getArchiveSlug());
			
			$post['terms'] = $postTerms;
		}
		// Сформируем полную ссылку на пост, учитывая иерархию терминов к которым принадлежит запись
		$this->postPermalink($post, $termsOnId, $termsOnParent, $postTerms);
		// dd($post);
		
		// Если правильная ссылка на запись и пришедшая не совпадают - отправляем по правильному адресу
		// if(\langUrl(FULL_URL) != $post['url']){
			// $this->request->location($post['url']);
		// }
		
		// Указываем что выводить данную запись следует шаблоном single
		// $this->view->is('single');
		$post = applyFilter('before_return_post', $post);
		
		return $post;
	}
	
	// public function actionList(Request $request)
	public function actionList(Request $request, $page = 1, $tslug = null, $taxonomy = null, $limit = null)
	{
		$hierarchy = explode('/', $tslug);
		
		$result = $taxonomy ? $this->getPostsWithTaxonomy($taxonomy, $hierarchy)
							: $this->getPostsWithoutTaxonomy();
		
		[$list, $termsFromExistsPost, $termsByPostsIds, $allTerms] = $result;
		if (!$list) {
			abort(404);
		}
		unset($result);
				
		$this->setPermalinkAndTerms($list, $termsByPostsIds, $termsFromExistsPost);
		$this->createFilters($termsByPostsIds);
		$this->createBreadCrumbs($this->post, $taxonomy, $hierarchy, $termsByPostsIds, $tslug);
		
		$this->post['breadcrumbs'] 	= $this->breadcrumbs;
		$this->post['pagination'] 	= $this->pagination($page);
		$this->post['__model'] 		= $this->model;
		$list 						= $this->fillMeta($list);
		$list 						= applyFilter('before_return_post', $list);
		
		$this->post['__list'] = $list;
		unset($list);
		
		return viewWrap('list', $this->post, ['post' => $this->post]);
    }
	
	private function fillMeta($posts)
	{
		$postsOnId = Arr::itemsOnKeys($posts, ['id']);
		$ids = array_keys($postsOnId);
		
		$meta = $this->model->getRawMeta($ids);
		
		$comments = [];
		$commnetsOnId = $comments ? Arr::itemsOnKeys($comments, ['comment_post_id']) : [];
		
		if ($meta) {
			$posts = $mediaIds = [];
			foreach ($meta as $m) {
				if ($m->meta_key == $this->img)
					$mediaIds[$m->post_id] = $m->meta_value;
				$postsOnId[$m->post_id][0]->{$m->meta_key} = $m->meta_value;
			}
			
			if (!empty($mediaIds)) {
				$media = DB::table('media')->whereIn('id', $mediaIds)->get();
				$mediaOnId = Arr::itemsOnKeys($media, ['id']);
				foreach ($mediaIds as $postId => $mediaId) {
					$postsOnId[$postId][0]->{$this->img} = $mediaOnId[$mediaId][0]->src;
					$postsOnId[$postId][0]->{$this->img . '_meta'} = unserialize($mediaOnId[$mediaId][0]->meta);
				}
			}
			
			foreach($postsOnId as $post){
				$post = $post[0];
				$post->comment_count = isset($commnetsOnId[$post->id]) ? count($commnetsOnId[$post->id]) : 0;
				$posts[] = $post;
			}
		}
		return $posts;
	}
	
	private function isFront()
	{
		if (!isset($this->post->id)) {
			return false;
		}
		
		return Config::get('front_page') == $this->post->id;
	}
	
	private function createFilters($termsByPostsIds)
	{
		$archiveTerms = $termsByPostsIds[array_key_first($termsByPostsIds)];
		if (!$archiveTerms) return;
		[$termsOnId, $termsOnParent] = Arr::itemsOnKeys($archiveTerms, ['id', 'parent']);
		$postTerms = $this->postTermsLink($termsOnId, $termsOnParent, $archiveTerms);
		
		if($postTerms) {
			$this->post['filters'] = $postTerms;
			// $this->post['filters'] = Common::archiveTermsHTML(array_reverse($postTerms), Options::getArchiveSlug());
		}
	}
	
	private function pagination($page)
	{
		return (new Pagination())->run(url()->current(), $page, /*$this->model->getAllItemsCount()*/ 10, /*$this->postOptions['rewrite']['paged']*/ 3);
	}
	
	
	private function setPermalinkAndTerms(&$posts, &$termsByPostsIds, $termsFromExistsPost)
	{
		[$termsOnId, $termsOnParent] = Arr::itemsOnKeys($termsFromExistsPost, ['id', 'parent']);
		foreach($posts as &$post){
			if(!isset($termsByPostsIds[$post->id])) $termsByPostsIds[$post->id] = false;
			$this->postPermalink($post, $termsOnId, $termsOnParent, $termsByPostsIds[$post->id] ?? false);
			// $post['terms'] = Common::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $termsByPostsIds[$post['id']]), PostsTypes::getArchiveSlug());
			$post->terms = $termsByPostsIds[$post->id];
		}
	}
	
	private function getPostsWithoutTaxonomy()
	{
		if(!$posts = $this->model->getByType($this->postOptions['type'])) abort(404);
		$termsFromExistsPost = !empty($this->postOptions['taxonomy']) ? $this->model->taxonomy->getAllByObjectsIds(array_keys(Arr::itemsOnKeys($posts, ['id']))) : [];
		
		$termsByPostsIds = $termsFromExistsPost ? Arr::itemsOnKeys($termsFromExistsPost, ['object_id']) : [];
		
		// Получаем все таксы добавленные в базу, даже если для них нету постов
		$allTerms = $this->model->taxonomy->getByTaxonomies();
		
		return [$posts, $termsFromExistsPost, $termsByPostsIds, $allTerms];
	}
	
	private function getPostsWithTaxonomy($taxonomy, $hierarchy)
	{
		$allTerms = $this->model->taxonomy->getByTaxonomies();
		$termsFromExistsPost = $this->model->taxonomy->filter($allTerms, 'taxonomy', $taxonomy);
		
		$lastChild = $hierarchy[count($hierarchy) - 1];
		$findTerm = false;
		// get current selected term to know whence build hierarchy
		
		foreach($allTerms as $term){
			if($term->slug == $lastChild){
				$currentTerm = $term;
				$findTerm = true;
				break;
			}
		}
		
		if(!$findTerm) {
			exit('404: term not found');
		}
		
		list($termsOnIds, $termsOnParents) = Arr::itemsOnKeys($allTerms, ['id', 'parent']);
		$builtedTermsParentHierarchy = substr(str_replace('|', '/', self::builtHierarchyDown($termsOnIds, $currentTerm, 'slug') . '|' .$lastChild), 1);
		
		
		if (implode('/', $hierarchy) != $builtedTermsParentHierarchy) {
			exit('location: ' . $builtedTermsParentHierarchy);
		}
		
		$toShow = $termsOnParents[$currentTerm->id] ?? NULL;
		$i = 0;
		$termsTaxonomyIds[] = $currentTerm->term_taxonomy_id;
		
		while (isset($toShow[$i])) {
			$termsTaxonomyIds[] = $toShow[$i]->term_taxonomy_id;
			if (isset($termsOnParents[$toShow[$i]->id])) {
				$toShow = array_merge($toShow, $termsOnParents[$toShow[$i]->id]);
			}
			$i++;
		}
		
		$posts = $this->model->getPostsBysTermsTaxonomyIds($termsTaxonomyIds);
		$allTerms = $this->model->taxonomy->getAllByObjectsIds(array_keys(Arr::itemsOnKeys($posts, ['id'])));
		$termsByPostsIds = Arr::itemsOnKeys($allTerms, ['object_id']);
		
		return [$posts, $termsFromExistsPost, $termsByPostsIds, $allTerms];
	}
	
	private function postTermsLink($termsOnId, $termsOnParent, $termsByPostId, $mergeKey = 'slug')
	{
		if(!$termsByPostId) return;
		foreach($termsByPostId as $postTerm){
			$title = $this->postOptions['taxonomy'][$postTerm->taxonomy]['title'];
			if(!isset($postTerms[$title])) $postTerms[$title] = [];
			$postTerms[$title][$postTerm->name] = $postTerm->taxonomy . str_replace('|', '/', self::builtHierarchyDown($termsOnId, $postTerm, $mergeKey) . '|' . $postTerm->$mergeKey);
		}
		return $postTerms;
	}
	
	private function prepareArgs($action, $comeArgs)
	{
		$resArgs = [
			'type' 		=> $action['type'] ?? 'page',
			'taxonomy' 	=> $action['taxonomy'] ?? null,
			'tslug' 	=> null,
			'page' 		=> $resArgs['page'] ?? 1,
		];
		
		
		if (isset($action['args']) && is_array($action['args'])) {
			foreach ($action['args'] as $key => $arg) {
				$resArgs[$arg] = $comeArgs[$key + 1];
			}
		}
		
		$this->run($resArgs['type']);
		
		return $resArgs;
	}
	
	public static function builtHierarchyDown(&$itemsOnId, $current, $mergeKey, $level = 0)
	{
		if ($level > 10) exit('stop recursion');
		$hierarchy = '';
		if (isset($itemsOnId[$current->parent][0])) {
			$next = $itemsOnId[$current->parent][0];
			$hierarchy = self::builtHierarchyDown($itemsOnId, $next, $mergeKey, $level + 1) . '|' . $next[$mergeKey];
		}
		return $hierarchy;
	}
	
	
	public function postPermalink(&$post, $termsOnId, $termsOnParent, $termsByPostId, $slug = false)
	{
		$permalink 	 = \url('/') . ($slug ?: '/' . trim(PostsTypes::getCurrent()['rewrite']['slug'], '/')) . '/' . $post->slug;
		$post->slug = $post->permalink = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
	}
	
	private function createBreadCrumbs(&$post, $taxonomy, $hierarchy, $termsByPostsIds, $tslug = '')
	{
		//dd(func_get_args());
		// Узнаем имя таксономии по метке для хлебных крошек
		$taxonomyName = [];
		
		foreach ($hierarchy as $section) {
			foreach ($termsByPostsIds as $term) {//dd($termsByPostsIds, $term, $section);
				if ($term && $term[0]->slug == $section) {
					$taxonomyName[] = $term[0]->name;
					break;
				}
			}
		}
		
		if (empty($taxonomyName)) {
			$taxonomyName = $tslug;
		}
		
		$value = $type = $taxonomyName;
		
		$taxonomyTitle = $taxonomy ? $this->postOptions['taxonomy'][$taxonomy]['title'] : '';
		// dd($this->postOptions);
		$this->breadcrumbs[url('/')] = 'Главная';
		if($this->postOptions['has_archive'] && !$this->postOptions['rewrite']['with_front']){
			$this->breadcrumbs[(url('/') . '/' . $this->postOptions['has_archive'])] = $this->postOptions['title'];
		}
		
		$post['short_title'] = isset($post['short_title']) && $post['short_title'] ? $post['short_title'] : $post['title'];
		if ($taxonomyName) {
			if (is_array($taxonomyName)) {
				$taxonomyName = implode(' > ', $taxonomyName);
			}
			$this->addBreadCrumbsHelper($taxonomyTitle, $taxonomyName, $taxonomyTitle, $post['short_title']);
		} elseif (isset($post['id']) && Config::get('front_page') != $post['id']) {
			$this->breadcrumbs[$post['slug']] = $post['short_title'];
			
			if($this->postOptions['title']){
				$post['h1'] = $post['title'];
				$post['title'] .= ' - ' . $this->postOptions['title'];
			}	
		}
		
		Config::set('breadcrumbs', $this->breadcrumbs);
	}
	
	private function addBreadCrumbsHelper($taxonomyTitle, $value, $text, &$postTitle)
	{
		//dd(func_get_args());
		$this->breadcrumbs[$taxonomyTitle] = $text . ': ' . $value;
		$postTitle = $taxonomyTitle . ': ' . $value . ' | ' . $postTitle;
	}
	
	public function __call($method, $args)
	{
		dd(func_get_args());
		[$method, $args] = explode('__', $method);
		$args = explode('00', $args);
		$postType = array_shift($args);
		$this->run($postType);
		
		// if ($method == 'actionSingle') {
			
		// } elseif ($method == 'actionList') {
			
		// }
		array_unshift($args, app()->get('request'));
		
		$this->$method(...$args);
	}
	
	public function foo()
	{
		dd(__METHOD__, func_get_args());
	}
}

