<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Post;
use App\Helpers\{PostsTypes, Arr, Pagination, Config};
use DB;

class PostController extends Controller
{
	private $img = '_jmp_post_img';
	
	private function setModel($type)
	{
		$this->model = new Post;
		PostsTypes::setCurrentType($type);
		$this->postOptions = $this->post = PostsTypes::getCurrent();
		$this->breadcrumbs = [];
	}
	
    public function actionIndex(){
        
    }
	
	public function actionSingle(Request $request, $url)
	{
		extract($this->prepareArgs($request->route()->getAction(), func_get_args()) ?? []);
		dd(__METHOD__, get_defined_vars());
	}
	
	public function actionList(Request $request)
	{
		extract($this->prepareArgs($request->route()->getAction(), func_get_args()) ?? []); // get type / tslug / page / taxonomy
		$this->setModel($type ?? null);
		$this->post = $this->postOptions;
		$hierarchy = explode('/', $tslug);
		
		$result = $taxonomy ? $this->getPostsWithTaxonomy($taxonomy, $hierarchy)
							: $this->getPostsWithoutTaxonomy();
		
		[$this->post['__list'], $termsFromExistsPost, $termsByPostsIds, $allTerms] = $result;
		unset($result);
				
		$this->setPermalinkAndTerms($this->post['__list'], $termsByPostsIds, $termsFromExistsPost);
		$this->createFilters($termsByPostsIds);
		$this->createBreadCrumbs($list, $taxonomy, $hierarchy, $termsByPostsIds, $tslug);
		
		$this->post['pagination'] 	= $this->pagination($page);
		$this->post['__model'] 		= $this->model;
		$this->post['__list'] 		= $this->fillMeta($this->post['__list']);
		$this->post['__list'] 		= applyFilter('before_return_post', $this->post['__list']);
		
		return view('list', ['post' => $this->post]);
    }
	
	private function fillMeta($posts)
	{
		$postsOnId = Arr::itemsOnKeys($posts, ['id']);
		$ids = array_keys($postsOnId);//dd($postsOnId);
		
		$meta = DB::select('Select post_id, meta_key, meta_value from postmeta where post_id IN('.implode(',', $ids).')');
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
				$media = DB::select('Select * from media where id IN ('.implode(',', $mediaIds).')');
				$mediaOnId = Arr::itemsOnKeys($media, ['id']);
				foreach ($mediaIds as $postId => $mediaId) {
					$postsOnId[$postId][0][$this->img] = $mediaOnId[$mediaId][0]['src'];
					$postsOnId[$postId][0][$this->img . '_meta'] = unserialize($mediaOnId[$mediaId][0]['meta']);
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
		$resArgs['type'] = $action['type'];
		$resArgs['taxonomy'] = $action['taxonomy'] ?? null;
		$resArgs['tslug'] = null;
		$resArgs['page'] = $resArgs['page'] ?? 1;
		
		
		if (isset($action['args']) && is_array($action['args'])) {
			foreach ($action['args'] as $key => $arg) {
				$resArgs[$arg] = $comeArgs[$key + 1];
			}
		}
		
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
		$permalink 	 = \URL::to('/') . ($slug ?: '/' . trim(PostsTypes::getCurrent()['rewrite']['slug'], '/')) . '/' . $post->slug;
		$post->slug = $post->permalink = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
	}
	
	private function createBreadCrumbs(&$post, $taxonomy, $hierarchy, $termsByPostsIds, $tslug)
	{
		//dd(func_get_args());
		// Узнаем имя таксономии по метке для хлебных крошек
		$taxonomyName = [];
		
		foreach ($hierarchy as $section) {
			foreach ($termsByPostsIds as $term) {//dd($termsByPostsIds, $term, $section);
				if ($term[0]->slug == $section) {
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
		
		if($this->postOptions['has_archive'] && !$this->postOptions['rewrite']['with_front']){
			$this->breadcrumbs[($this->postOptions['has_archive'])] = $this->postOptions['title'];
		}
		
		$post['short_title'] = isset($post['short_title']) && $post['short_title'] ? $post['short_title'] : $post['title'];
		if ($type) {
			if (is_array($value)) {
				$value = implode(' > ', $value);
			}
			$this->addBreadCrumbsHelper($taxonomyTitle, $value, $taxonomyTitle, $post['short_title']);
		} elseif (isset($post['id']) && $this->config->front_page != $post['id']) {
			$this->breadcrumbs[$post['slug']] = $post['short_title'];
			
			if($this->postOptions['title']){
				$post['h1'] = $post['title'];
				$post['title'] .= ' - ' . $this->postOptions['title'];
			}	
		}
		
		$this->post['breadcrumbs'] = $this->breadcrumbs;
	}
	
	private function addBreadCrumbsHelper($taxonomyTitle, $value, $text, &$postTitle)
	{
		//dd(func_get_args());
		$this->breadcrumbs[$taxonomyTitle] = $text . ': ' . $value;
		$postTitle = $taxonomyTitle . ': ' . $value . ' | ' . $postTitle;
	}
}

