<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController as FrontPostController;
use App\Post as FrontPost;
use App\Admin\Post as AdminPost;
use App\Admin\Menu;
use App\Helpers\Arr;
use App\Helpers\PostsTypes;
use Options;
use DB;

class MenuController extends Controller{
	public function __construct(){
		addAction('admin_footer', function(){
			echo '
			<script src="/admin_static/js/edit-menu.js"></script>
			<script src="/admin_static/js/jquery.nestable.js"></script>';
		});
	}
	
	public function actionIndex()
	{
		// creating new menu
		
		
		if (request()->has('new_menu')) {
			$error 	 = '';
			$newMenu = trim(request()->get('new_menu'));
			
			if (!$newMenu) {
				redirBack('Введите название меню');
			}
			
			if(!$this->add($newMenu)){
				redirBack('Меню с таким названием уже существует');
			}
						
			redirBack('Меню успешно создано', 'menu');
		} elseif (request()->has('del_menu_id')) {
			$this->del(request()->get('del_menu_id'));
		}
			
		$data['title'] = 'Меню';
		
		$posts 					= AdminPost::orderBy('created_at')->get();
		$postsOnTypes 			= Arr::itemsOnKeys($posts, ['post_type']);
		$post 					= new AdminPost;
		$postFront 				= new FrontPost;
		$postControllerFront 	= new FrontPostController;
		
		if (!empty($postsOnTypes)) {
			$allTaxonomies = $allPostIds = $taxonomies = [];
			
			foreach ($postsOnTypes as $postType => &$posts) {
				if (!$options = PostsTypes::get(null, $postType)) {
					continue;
				}
				
				$data['types'][$options['type']] = $options;
				
				if (isset($options['taxonomy'])) {
					foreach($options['taxonomy'] as &$tax) {
						$tax['archive'] = $options['has_archive'];
					}
					
					$taxonomies = array_merge($taxonomies, $options['taxonomy']);
				}
				
				if($options['hierarchical'])
					$posts = $post->listForParents($posts, NULL, TRUE);
				else{
					$allPostIds 	= array_merge($allPostIds, Arr::getKeys($posts, 'id'));
					$allTaxonomies 	= array_merge($allTaxonomies, array_keys($options['taxonomy'] ?? []));
				}
			}
			
			$postsTerms = $post->taxonomy->getByTaxonomies($allTaxonomies);
			
			
			
			$relatedPostTerms = $postFront->taxonomy->relation()
						->whereIn('tr.object_id', $allPostIds)
						->whereIn('tt.taxonomy', $allTaxonomies)
						->get()->toArray();
						
			
			$relatedPostTerms = Arr::itemsOnKeys($relatedPostTerms, ['object_id']);
			
			list($termsOnId, $termsOnParent, $termsOnTaxonomies) = Arr::itemsOnKeys($postsTerms, ['id', 'parent', 'taxonomy']);
			
			foreach ($postsOnTypes as $postType => &$posts) {
				$options = $data['types'][$postType] ?? null;
				$postControllerFront->postOptions = $options;
				
				if($options && !$options['hierarchical']) {
					foreach($posts as &$onePost) {
						$postControllerFront->postPermalink($onePost, $termsOnId, $termsOnParent, $relatedPostTerms[$onePost['id']] ?? null, $options['rewrite']['slug']);
					}
				}
			}
			
			if (!empty($taxonomies)) {
				$data['types']['taxonomies'] = $taxonomies;
				
				foreach ($taxonomies as $tax1 => $opt) {
					$postsOnTypes['taxonomies'][$tax1] = $post->hierarchyItems($termsOnTaxonomies[$tax1]);
				}
			}
		}
		
		$data['posts_on_types'] = $postsOnTypes;
		$data['menus'] 			= $this->menus();
		$data['menuItems'] 		= DB::table('menu')->where('menu_id', $data['menus']['id'])->get()->toArray();
		
		return view('menu', compact('data'));
	}
	
	public function actionEdit()
	{
		if (empty($menu = json_decode($_POST['menu']))) {
			return;
		}
		
		$menuId 	= (int)$_POST['menu_id'];
		$queryItems = []; 
		$sort 		= $subSort = 0;
		
		foreach ($menu as $item) {
			$queryItems[] = $this->cols($item, -1, $sort++, $menuId);
			
			if (isset($item->children)) {
				$subSort = 0;
				
				foreach($item->children as $childItem){
					$queryItems[] = $this->cols($childItem, $item->id, $subSort++, $menuId);
				}
			}
		}
		
		DB::table('menu')->where('menu_id', $menuId)->delete();
		Menu::insert($queryItems);
		
		if (file_exists($unlinkFile = themeDir() . '/uploads/cache/menu/menu.html')) {
			unlink($unlinkFile);
		}
	}
	
	public function del($id)
	{
		$menus = Options::get('menu', true);
		
		
		if (count($menus) == 1) {
			redirBack('Последнее меню удалять нельзя, отредактируйте его или создайте новое.');
		} else {
			$newMenu = array();
			
			foreach ($menus as $k => $menu) {
				if ($menu['id'] == $id) {
					$issetThisMenu = true;
					continue;
				}
				
				$newMenu[] = $menu;
			}
			
			if (!isset($issetThisMenu)) {
				exit('Меню не найдено!');
			}
			
			Options::save('menu', $newMenu, true);
			Options::save('menu_active_id', $newMenu[0]['id']);
				
			DB::table('menu')->where('menu_id', $id)->delete();
		}
		
		redirBack('Выбранное меню успешно удалено', 'menu');
	}
	
	public function actionActivate()
	{
		Options::save('menu_active_id', (int)$_POST['id']);
	}
	
	public function actionSelect()
	{
		if(!isset($_POST['id']) || !is_numeric($_POST['id'])) exit;
		exit(json_encode(DB::table('menu')->where('menu_id', $id)->get()));
	}
	
	
	private function menus()
	{
		$menu['list'] = Options::get('menu', true);
		$menu['id']   = Options::get('menu_active_id');
		
		return $menu;
	}
	
	private function add($name)
	{
		$menus = $this->menus() ?: array();
		$menus = $menus['list'];
		$isset = false;
		$maxId = 0;
		
		if ($menus) {
			$maxId = 1;
			
			foreach ($menus as $menu) {
				if($menu['name'] == $name){
					$isset = true;
					break;
				}
				
				if($menu['id'] > $maxId) {
					$maxId = $menu['id'];
				}
			}
		}
		
		if (!$isset) {
			$menu 		= array('id' => ++$maxId, 'name' => $name);
			$empty 		= empty($menus);
			$menus[] 	= $menu;
			
			Options::save('menu', $menus, true);
		}
		
		return !$isset;
	}
	
	private function cols($item, $parent, $sort, $menuId)
	{
		return [
			'menu_id' 	=> $menuId, 
			'object_id' => $item->id, 
			'name' 		=> $item->name, 
			'origname' 	=> $item->origname, 
			'url' 		=> $item->url, 
			'type' 		=> $item->type, 
			'parent' 	=> $parent, 
			'sort' 		=> $sort
		];
	}
	
	private function cols1($item, $parent, $sort, $menuId)
	{
		return "(
			{$menuId},
			{$item->id},
			'{$item->name}',
			'{$item->origname}',
			'{$item->url}',
			'{$item->type}',
			{$parent},
			{$sort}
		),";
	}
}