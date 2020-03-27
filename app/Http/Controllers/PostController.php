<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\{Post, Taxonomy};
use App\Helpers\{PostsTypes, Arr, Pagination};
use DB;
use Options;

class PostController extends Controller
{
	private $img;
	private $launched;
	private $template;
	
	public function run($type, $method = null, $args = null)
	{
		// dd(func_get_args(), get_defined_vars());
		if ($this->launched) return;
		$this->launched = true;
		$this->img = Options::get('_img');
		$this->model = new Post;
		PostsTypes::setCurrentType($type);
		$this->postOptions = $this->post = $this->model->postOptions = PostsTypes::getCurrent();
		$this->breadcrumbs = [];
		
		if ($method) {		
			return $this->$method(...$args);	
		}
	}
	
    public function actionIndex()
	{
		$frontPage = Options::get('front_page');
		if (is_numeric($frontPage)) {
			$this->run('page');
			return $this->actionSingle(NULL, $frontPage);
		} else {
			$this->run('post');
			return $this->last();
		}
    }
	
	public function last()
	{
		$this->template = 'front';
		return $this->actionList();
	}
	
	public function actionSingle($slug, $id = null)
	{
		if ($id) {
			$post = Post::findOrFail($id);
		} else {
			$hierarchy 	= explode('/', $slug);
			$slug 		= array_pop($hierarchy); // get last url part
			
			if (!$post = Post::whereSlug($slug)->first()) {
				abort(404);
			}
		}
		
		$this->run($post->post_type);
		$post->getMeta(true);
		
		// If this post is the front
		if ($this->checkFrontPageAliase($post['id'])) {
			return viewWrap($this->getTemplate('single'), $post)->with('post', $post);
		}
		
		// If type of this post related taxonomy
		if (!$this->postOptions['hierarchical']) {
			$post = $this->taxonomyPost($post);
		} else { // If type of this post is hierarchical structure, check hierarchy
			if (!empty($hierarchy)) {
				if ($redirectResponce = $this->checkHierarchy($post['slug'], $post['parent'], $hierarchy)) {
					return $redirectResponce;
				}
			} elseif ($post['parent']) {
				abort(404);
			}
		}
		
		$this->createBreadCrumbs($post);
		$post = applyFilter('before_return_post', $post);
		return viewWrap($this->getTemplate('single'), $post)->with('post', $post);
	}
	
	private function checkFrontPageAliase($postId)
	{
		if ($postId == Options::get('front_page')) {
			if(url('/') . '/' != urlWithoutParams()) {
				redir(url('/'));
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
			if ($hierarchy) abort(404);
		} else {
			if (!$hierarchy) {
				// взять все страницы, создать иерархию и перенаправить
				redir(url('/') . $this->getParentHierarchy($parent, $this->model->getByType('page'),  'slug') . '/' . $slug);
			} else {
				$parents = DB::table('posts')
							->select('id', 'title', 'short_title', 'slug', 'parent')
							->whereIn('slug', $hierarchy)
							->orderBy('parent', 'desc')
							->get();
							
				if (count($parents) < count($hierarchy)) {
					abort(404);
				} else {
					$h = array_reverse($hierarchy);
					$tempParent = $parent;
					$i = 0;
					$addBreadCrumbs = [];
					
					foreach ($parents as $parent) {
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
		foreach ($items as $item) {
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
			[$termsOnId, $termsOnParent] 	= Arr::itemsOnKeys($terms, ['id', 'parent']);
			
			// Получим термины относящиеся к данной записи, которые привязаны к таксономиям данного типа записи
			$postTerms = $this->model->getPostTerms($post['id'], array_keys($this->postOptions['taxonomy']));
			
			if (count($postTerms)) {
				$post['termsHtml'] = Arr::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $postTerms), $this->postOptions['has_archive']);
				$post['terms'] = $postTerms;
			} else {
				$postTerms = [];
			}
		}
		
		// Сформируем полную ссылку на пост, учитывая иерархию терминов к которым принадлежит запись
		$this->postPermalink($post, $termsOnId, $termsOnParent, $postTerms);
		
		// Если правильная ссылка на запись и пришедшая не совпадают - отправляем по правильному адресу
		if(url()->current() != $post['permalink']){
			redir($post['permalink']);
		}
		
		return $post;
	}
	
	public function actionList($page = 1, $taxonomy = null, $tslug = null, $limit = null)
	{
		$this->model->setOffset($page, $limit ?? $this->postOptions['rewrite']['paged'] ?? null);
		$hierarchy = explode('/', $tslug);
		$result = $taxonomy ? $this->getPostsWithTaxonomy($taxonomy, $hierarchy)
							: $this->getPostsWithoutTaxonomy();
		
		[$list, $termsFromExistsPost, $termsByPostsIds, $allTerms] = $result;
		if (!$list) abort(404);
		unset($result);
		
		$this->setPermalinkAndTerms($list, $termsByPostsIds, $termsFromExistsPost);
		$this->createFilters($allTerms);
		$this->createBreadCrumbsForList($this->post, $taxonomy, $hierarchy, $termsByPostsIds, $tslug);
		
		$this->post['pagination'] 	= $this->pagination($page);
		$this->post['__model'] 		= $this->model;
		$list 						= $this->fillMeta($list);
		$list 						= applyFilter('before_return_posts', $list);
		
		$this->post['__list'] 		= $list;
		unset($list);
		
		return viewWrap($this->getTemplate('list'), $this->post, ['post' => $this->post]);
    }
	
	private function getTemplate($default)
	{
		return $this->template ?? $default;
	}
	
	private function fillMeta($posts)
	{
		$postsOnId = Arr::itemsOnKeys($posts, ['id']);
		$ids = array_keys($postsOnId);
		
		$meta = $this->model->getRawMeta($ids);
		
		$comments = [];
		$commentsOnId = $comments ? Arr::itemsOnKeys($comments, ['comment_post_id']) : [];
		
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
				$post->comment_count = isset($commentsOnId[$post->id]) ? count($commentsOnId[$post->id]) : 0;
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
		
		return Options::get('front_page') == $this->post->id;
	}
	
	private function createFilters($archiveTerms)
	{
		
		// dd($termsFromExistsPost);
		// foreach ($termsFromExistsPost as $term) {
			// if (!isset($archiveTerms1[$term->slug])) {
				// $archiveTerms1[$term->slug] = false;
				// $archiveTerms[] = $term;
			// }
		// } 
		
		// dd($archiveTerms);
		// $archiveTerms = $termsByPostsIds[array_key_first($termsByPostsIds)];dd($archiveTerms);
		if (!$archiveTerms) return;
		[$termsOnId, $termsOnParent] = Arr::itemsOnKeys($archiveTerms, ['id', 'parent']);
		$postTerms = $this->postTermsLink($termsOnId, $termsOnParent, $archiveTerms);
		// dd($postTerms);
		if($postTerms) {
			$this->post['filters'] = Arr::archiveTermsHTML(array_reverse($postTerms), $this->postOptions['has_archive']);
		}
	}
	
	private function pagination($page)
	{
		return (new Pagination())->run(url()->current(), $page, $this->model->getAllItemsCount(), $this->postOptions['rewrite']['paged']);
	}
	
	
	private function setPermalinkAndTerms(&$posts, &$termsByPostsIds, $termsFromExistsPost)
	{
		[$termsOnId, $termsOnParent] = Arr::itemsOnKeys($termsFromExistsPost, ['id', 'parent']);
		foreach($posts as &$post){
			if(!isset($termsByPostsIds[$post->id])) $termsByPostsIds[$post->id] = false;
			$this->postPermalink($post, $termsOnId, $termsOnParent, $termsByPostsIds[$post->id] ?? false);
			if ($termsByPostsIds[$post->id])
				$post->terms = Arr::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $termsByPostsIds[$post->id]), $this->postOptions['has_archive']);
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
		$allTerms 				= $this->model->taxonomy->getByTaxonomies();
		$termsFromExistsPost 	= $this->model->taxonomy->filter($allTerms, 'taxonomy', $taxonomy);
		$lastChild 				= $hierarchy[count($hierarchy) - 1];
		$findTerm 				= false;
		
		// get current selected term to know whence build hierarchy
		foreach($allTerms as $term){
			if($term->slug == $lastChild){
				$currentTerm = $term;
				$findTerm = true;
				break;
			}
		}
		
		if(!$findTerm) abort(404);
		
		[$termsOnIds, $termsOnParents] = Arr::itemsOnKeys($allTerms, ['id', 'parent']);
		$builtedTermsParentHierarchy = substr(str_replace('|', '/', Arr::builtHierarchyDown($termsOnIds, $currentTerm, 'slug') . '|' .$lastChild), 1);
		
		
		if (implode('/', $hierarchy) != $builtedTermsParentHierarchy) {
			redir($builtedTermsParentHierarchy);
		}
		
		$toShow 			= $termsOnParents[$currentTerm->id] ?? NULL;
		$termsTaxonomyIds[] = $currentTerm->term_taxonomy_id;
		$i 					= 0;
		
		while (isset($toShow[$i])) {
			$termsTaxonomyIds[] = $toShow[$i]->term_taxonomy_id;
			if (isset($termsOnParents[$toShow[$i]->id])) {
				$toShow = array_merge($toShow, $termsOnParents[$toShow[$i]->id]);
			}
			$i++;
		}
		
		$posts 				= $this->model->getPostsBysTermsTaxonomyIds($termsTaxonomyIds);
		$allTerms 			= $this->model->taxonomy->getAllByObjectsIds(array_keys(Arr::itemsOnKeys($posts, ['id'])));
		$termsByPostsIds 	= Arr::itemsOnKeys($allTerms, ['object_id']);
		
		return [$posts, $termsFromExistsPost, $termsByPostsIds, $allTerms];
	}
	
	private function postTermsLink($termsOnId, $termsOnParent, $termsByPostId, $mergeKey = 'slug')
	{
		if(!$termsByPostId) return;
		foreach($termsByPostId as $postTerm){
			$title = $this->postOptions['taxonomy'][$postTerm->taxonomy]['title'];
			if(!isset($postTerms[$title])) $postTerms[$title] = [];
			$postTerms[$title][$postTerm->name] = $postTerm->taxonomy . str_replace('|', '/', Arr::builtHierarchyDown($termsOnId, $postTerm, $mergeKey) . '|' . $postTerm->$mergeKey);
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
	
	
	public function postPermalink(&$post, $termsOnId, $termsOnParent, $termsByPostId, $slug = false)
	{
		// dd($this->postOptions, $slug);
		$permalink 	 = url('/') . '/' . ($slug ? $slug . '/' : ($this->postOptions['rewrite']['slug'] ? $this->postOptions['rewrite']['slug'] . '/' : '/')) . $post->slug;
		$post->slug = $post->permalink = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
	}
	
	private function createBreadCrumbsForList(&$post, $taxonomy, $hierarchy, $termsByPostsIds, $tslug = '')
	{
		// Узнаем имя таксономии по метке для хлебных крошек
		$taxonomyName = [];
		if ($termsByPostsIds)
		{
			foreach ($hierarchy as $section) {
				foreach ($termsByPostsIds as $term) {
					if (!isset($term->slug)) $term = $term[0];
					if ($term && $term->slug == $section) {
						$taxonomyName[] = $term->name;
						break;
					}
				}
			}
		}
		
		if (empty($taxonomyName)) {
			$taxonomyName = $tslug;
		}
		
		$value 							= $type = $taxonomyName;//dd($taxonomy);
		$taxonomyTitle 					= $taxonomy ? $this->postOptions['taxonomy'][$taxonomy]['title'] : '';
		
		
		$this->createBreadCrumbs($post, $taxonomyTitle, $taxonomyName);
	}
	
	private function createBreadCrumbs(&$post, $taxonomyTitle = null, $taxonomyName = null)
	{
		$breadcrumbs[url('/')] = 'Главная';
		if($this->postOptions['has_archive'] && !$this->postOptions['rewrite']['with_front']){
			$breadcrumbs[(url('/') . '/' . $this->postOptions['has_archive'])] = $this->postOptions['title'];
		}
		
		$post['short_title'] 			= isset($post['short_title']) && $post['short_title'] ? $post['short_title'] : $post['title'];
		if ($taxonomyName) {//dd(!!$taxonomyName, $taxonomyTitle, $taxonomyName, $taxonomyTitle);
			if (is_array($taxonomyName)) {
				$taxonomyName = implode(' > ', $taxonomyName);
			}
			$this->addBreadCrumbsHelper($post, $taxonomyTitle, $taxonomyName, $taxonomyTitle);
		} elseif (isset($post['id']) && Options::get('front_page') != $post['id']) {
			$breadcrumbs[$post['slug']] = $post['short_title'];
			
			if($this->postOptions['title']){
				$post['h1'] = $post['title'];
				$post['title'] .= ' - ' . $this->postOptions['title'];
			}	
		}
		$post['breadcrumbs'] = $this->getBreadcrumbsHtml(array_merge($breadcrumbs, $post['breadcrumbs'] ?? []));
	}
	
	private function addBreadCrumbs1(&$post, $taxonomyTitle = null, $value = null, $type = null){//dd(func_get_args());
		if($this->options['has_archive'] && !Options::front()){
			$this->config->addBreadCrumbs(\langUrl() . $this->options['has_archive'], $this->options['title']);
		}
		
		
		$post['short_title'] = isset($post['short_title']) && $post['short_title'] ? $post['short_title'] : $post['title'];
		if($type){
			if(is_array($value)) $value = implode(' > ', $value);
			$this->addBreadCrumbsHelper($taxonomyTitle, $value, $taxonomyTitle, $post['short_title']);
		}elseif(isset($post['id']) && $this->config->front_page != $post['id']){
			$this->config->addBreadCrumbs($post['url'], $post['short_title']);
			if($this->options['title']){
				$post['h1'] = $post['title'];
				$post['title'] .= ' - ' . $this->options['title'];
			}
				
		}
			
	}
	
	private function addBreadCrumbsHelper(&$post, $taxonomyTitle, $taxonomyName)
	{
		$a = $post['breadcrumbs'] ?? [];
		$a[] = ($taxonomyTitle ? $taxonomyTitle . ': ' : '') . $taxonomyName . (isset($post['has_archive']) ? '' : ' > ' . $post['short_title']);
		$post['breadcrumbs'] = $a;
	}
	
	
	
	public function getBreadcrumbsHtml(array $bc): string
	{
		$bcCount 	= count($bc);
		$i 			= 0;
		$html 		= '<div id="breadcrumbs">';
		foreach ($bc as $url => $name) {
			$html .= (++$i == $bcCount ? $name : "<a href=\"{$url}\">{$name}</a> > ");
		}
		
		return $html . '</div>';
	}
}

