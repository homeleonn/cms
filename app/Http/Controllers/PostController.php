<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Helpers\PostsTypes;
use App\Helpers\Arr;
use DB;

class PostController extends Controller
{
    public function actionIndex(){
        
    }
	
	public function actionList(Request $request)
	{
		$post = new Post;
		
		extract($this->prepareArgs($request->route()->getAction(), func_get_args()) ?? []); // get type / tslug / page / taxonomy
		PostsTypes::setCurrentType($type ?? null);
		
		$hierarchy = explode('/', $tslug);
		
		if (!$taxonomy) {
			$result = $this->getPostsWithoutTaxonomy($post, $type);
		} else {
			$result = $this->getPostsWithTaxonomy($post, $taxonomy, $hierarchy);
		}
		
		[$posts, $termsFromExistsPost, $termsByPostsIds, $allTerms] = $result;
		// dump($posts, $termsFromExistsPost, $termsByPostsIds, $allTerms, PostsTypes::getCurrent());
		// dd(PostsTypes::getCurrent(), $taxonomy, \URL::to('/'));
		
		
		[$termsOnId, $termsOnParent] = Arr::itemsOnKeys($termsFromExistsPost, ['id', 'parent']);
		foreach($posts as &$post){
			if(!isset($termsByPostsIds[$post->id])) $termsByPostsIds[$post->id] = false;
			$this->postPermalink($post, $termsOnId, $termsOnParent, $termsByPostsIds[$post->id] ?? false);
			// $post['terms'] = Common::termsHTML($this->postTermsLink($termsOnId, $termsOnParent, $termsByPostsIds[$post['id']]), PostsTypes::getArchiveSlug());
		}
		
		
		
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
			$taxonomyName = $taxonomySlug;
		}
		
		$taxonomyTitle = $taxonomy ? PostsTypes::getCurrent()['taxonomy'][$taxonomy]['title'] : '';
		
		// $this->addBreadCrumbs($list, $taxonomyTitle, $taxonomyName, $taxonomyName);
		
		// $list['pagenation'] = $this->pagination();
		
		//dd($list);
		$archiveTerms = $termsByPostsIds[array_key_first($termsByPostsIds)];
		// dd($archiveTerms, array_key_first($archiveTerms));
		[$termsOnId, $termsOnParent] = Arr::itemsOnKeys($archiveTerms, ['id', 'parent']);
		$postTerms = $this->postTermsLink($termsOnId, $termsOnParent, $archiveTerms);
		
		// $list[$listMark] = $this->fillMeta($list[$listMark]);
		
		// if($postTerms) {
			// $posts['filters'] = Common::archiveTermsHTML(array_reverse($postTerms), Options::getArchiveSlug());
		// }
		// $posts['__model'] = $this->model;
		// $this->view->is('list');
		// if($this->isFront){
			// $list['title'] = Common::getOption('title');
			// $list['description'] = Common::getOption('description');
		// }
		// $list[$listMark] = applyFilter('before_return_post', $list[$listMark]);
		dump($posts);
		// return $posts;
    }
	
	private function getPostsWithoutTaxonomy($post, $postType)
	{
		if(!$posts = $post->getByType($postType)) abort(404);
		$termsFromExistsPost = !empty(PostsTypes::get('taxonomy')) ? $post->taxonomy->getAllByObjectsIds(array_keys(Arr::itemsOnKeys($posts, ['id']))) : [];
		
		$termsByPostsIds = $termsFromExistsPost ? Arr::itemsOnKeys($termsFromExistsPost, ['object_id']) : [];
		
		// Получаем все таксы добавленные в базу, даже если для них нету постов
		$allTerms = $post->taxonomy->getByTaxonomies();
		
		return [$posts, $termsFromExistsPost, $termsByPostsIds, $allTerms];
	}
	
	private function getPostsWithTaxonomy($post, $taxonomy, $hierarchy)
	{
		$allTerms = $post->taxonomy->getByTaxonomies();
		$termsFromExistsPost = $post->taxonomy->filter($allTerms, 'taxonomy', $taxonomy);
		
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
		
		$posts = $post->getPostsBysTermsTaxonomyIds($termsTaxonomyIds);
		$allTerms = $post->taxonomy->getAllByObjectsIds(array_keys(Arr::itemsOnKeys($posts, ['id'])));
		$termsByPostsIds = Arr::itemsOnKeys($allTerms, ['object_id']);
		
		return [$posts, $termsFromExistsPost, $termsByPostsIds, $allTerms];
	}
	
	private function postTermsLink($termsOnId, $termsOnParent, $termsByPostId, $mergeKey = 'slug')
	{
		if(!$termsByPostId) return;
		foreach($termsByPostId as $postTerm){
			$title = PostsTypes::get('taxonomy')[$postTerm->taxonomy]['title'];
			if(!isset($postTerms[$title])) $postTerms[$title] = [];
			$postTerms[$title][$postTerm->name] = $postTerm->taxonomy . str_replace('|', '/', self::builtHierarchyDown($termsOnId, $postTerm, $mergeKey) . '|' . $postTerm->$mergeKey);
		}
		return $postTerms;
	}
	
	private function prepareArgs($action, $comeArgs)
	{
		$resArgs['type'] = $action['type'];
		$resArgs['taxonomy'] = $action['taxonomy'] ?? null;
		
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
}

