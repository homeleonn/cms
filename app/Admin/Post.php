<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\{Arr, PostsTypes};
use App\{Term, Taxonomy, Relationships};
use DB;
use Session;
use Options;

class Post extends Model
{
	const TEMPLATE = '/^[ \t\/*#@]*Template:(.*)$/mi';
	// protected $fillable = ['title', 'slug', 'short_title', 'content', 'post_type', 'comment_status', 'status', 'parent'];
    protected $fillable = ['title', 'slug', 'short_title', 'content', 'post_type', 'parent', 'author', 'status', 'comment_status'];
	public $taxonomy;
	
	public function relationship()
	{
		return $this->belongsToMany('App\Relationship', 'term_relationships', 'object_id', 'term_taxonomy_id');
	}
	
	public function __construct(...$args)
	{
		parent::__construct(...$args);
		$this->taxonomy = new \App\Taxonomy;
	}
	
	public function getAll()
	{
		return $this->where('post_type', $this->postOptions['type'])->get();
	}
	
	
    public function list()
	{
		if (!$posts = $this->getAllPosts()) abort(404);
		$addKeys = [];
		if(!$this->postOptions['hierarchical']){
			// Get posts terms
			$ids = Arr::column($posts, 'id');
			$addKeys['_terms'] = $this->getTermsByPostsId($ids);
			$this->_postTerms = $addKeys;
			$this->_allTerms = $this->taxonomy->getByTaxonomies();
			// Cache::set('postTerms', $addKeys['_terms']);
			
			// Get all terms for post taxonomies for cache(build link)
			// Cache::set('allTerms', $this->taxonomy->getByTaxonomies());
		}
		
		
		// build hierarchy
		$postsHierarchy = $this->hierarchyItems($posts, NULL, NULL, $this->_postTerms);
		
		$postsTable = $this->hierarchy($postsHierarchy, 'table');
		// dd($postsTable);
		return !$postsTable ? '' : '<table class="mytable posts '.$this->postOptions['type'].'" id="draggable"><tr align="center"><td>title/url</td>'.(($this->postOptions['taxonomy'] ?? false) ? '<td width="15%">Метки</td>' : '').'<td width="1%">Дата публикации</td></tr>' . $postsTable . '</table>';
	}
	
	public function getTermsByPostsId($ids)
	{
		$ids = is_array($ids) ? implode(',', $ids) : $ids;
		if(!$terms = \DB::select('Select t.*, tt.*, tr.object_id from terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tr.term_taxonomy_id = tt.term_taxonomy_id and object_id IN('.$ids.') order by t.id ASC')) return false;
		foreach($terms as $t){
			$termsByObject[$t->object_id][] = $t;
		}
		return $termsByObject;
	}
	
	/**
	 *  Return items like hierarchy
	 *  
	 *  @param int $selfId item id for which returns parents
	 *  @param int $parent item parent
	 */
	public function hierarchyItems($items, $selfId = NULL, $parent = NULL, $addKeys = [])
	{
		// dd($items, empty($items), !count($items));
		if(empty($items) || !count($items)){
			return [];
		}
		$isTerm = isset($items[0]->taxonomy);
		foreach ($items as $item) {
			if ($item->id == $selfId) continue;
			if (!empty($addKeys)) {
				foreach ($addKeys as $key => $values) {
					if (!$values) break;
					if (isset($addKeys[$key][$item->id])) {
						$item[$key] = $addKeys[$key][$item->id];
						unset($addKeys[$key][$item->id]);
					} else {
						$item[$key] = [];
					}
				}
			}
			$itemsToParents[$item->parent ?? 0][] = $item;
		}
		
		if (!isset($itemsToParents)) return [];
		ksort($itemsToParents);
		$itemsToParents = array_reverse($itemsToParents, true);
		if ($this->postOptions['hierarchical'] || $isTerm) {
			foreach ($itemsToParents as &$items) {
				foreach ($items as &$item) {
					if (isset($itemsToParents[$item->id])) {
						$item->children = $itemsToParents[$item->id];
						unset($itemsToParents[$item->id]);
					}
				}
			}
		}
		
		return $itemsToParents[0];
	}
	
	
	/**
	 *  form html post list like hierarchy
	 *  
	 *  @param array $posts 		array of posts
	 *  @param int $level 			hierarchy level
	 *  @param int $parent 			current post parent
	 *  @param string $type 		how output html
	 *  @param string $urlHierarchy built url hierarchy for link at each hierarchy level
	 *  
	 *  @return html code
	 */
	private function hierarchy($items, $type = 'select', $parent = 0, $level = 0, $urlHierarchy = ''){
		$html = '';
		foreach($items as $item){
			if($type == 'select'){
				$title = isset($item->title) ? $item->title : $item->slug;
				$html .= '<option '.($item->id == $parent ? 'selected' : '').' value="' . $item->id . '">' . str_repeat('&nbsp;', $level * 3) . ($level ? '&#8735;'  : '') . (mb_strlen($title) > 46 ? mb_substr($title, 0, 45) . '...' : $title) . '</option>';
			}elseif($type = 'table'){
				$html .= $this->hierarchyListHtml($item, $level, $urlHierarchy);
			}
			
			if(isset($item->children)){
				$urlHierarchy .= (isset($item->slug) ? $item->slug : $item->slug) . '/';
				$html .= $this->hierarchy($item->children, $type, $parent, $level + 1, $urlHierarchy);
			}
			
			if(!$item->parent)
				$urlHierarchy = '';
		}
		return $html;
	}
	
	private function hierarchyListHtml($item, $level, $urlHierarchy)
	{
		// d($item);
		$isPost = !isset($item->term_taxonomy_id);
		$buildLinkFlag = false;
		if($isPost && !$this->postOptions['hierarchical']){
			if(!empty($terms = Arr::itemsOnKeys($this->_allTerms, ['id', 'parent']))){
				list($termsOnId, $termsOnParent) = $terms;
				if (isset($this->_postTerms['_terms'][$item->id])) {
					$termsByPostId = $this->_postTerms['_terms'][$item->id];
					$permalink 	 = url('/') . '/' . trim($this->postOptions['rewrite']['slug'], '/') . '/' . $item->slug . '/';
					$item->slug = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
					$buildLinkFlag = true;
				}
			}
		}
		
		if ($isPost) {
			//$url = $item->slug;
			$url = ($this->postOptions['hierarchical'] ? '/' . $urlHierarchy . '/' . $this->postOptions['has_archive'] . $item->slug . '/' : ($buildLinkFlag ? $item->slug : '/' . $this->postOptions['has_archive'] . '/' . $item->slug . '/'));
		} else {
			$url = '/' . $this->postOptions['has_archive'] . '/' . $item->taxonomy . '/' . $urlHierarchy . $item->slug . '/' ;
		}
		// dd($item, $url);
		
		
		$link = '<a href="' . $url . '">перейти</a>';
		$edit = '<a href="' . url('/') . '/admin/' . $this->postOptions['type'] . '/' . (!$isPost ? 'term/' : '') . $item->id . '/edit/' . '">%s</a>';
		ob_start();
		?>
			<tr data-post_id="<?=$item->id?>">
				<td class="admin-page-list">
					<?=str_repeat('&mdash;', $level) . ' ' . sprintf($edit, $item->{$isPost ? ($item->short_title?'short_title':'title') : 'name'});?>
					<div style="position: absolute;">
						[<?=$link;?>]
						[<a href="#">свойства</a>]
						[<?=sprintf($edit, 'изменить');?>]
						[
							<form method="POST" action="<?=route($this->postOptions['type'] . ($isPost ? '.' : '.term_') . 'destroy', $item->id)?>" class="inline delitem">
								<button style="color: red;" class="but-as-link" title="<?=$this->postOptions['delete'] ?? 'Delete'?>" onclick="return confirm('Подтвердите удаление')">удалить</button>
							</form>
						]
					</div>
				</td>
				<?php
					if (isset($this->postOptions['taxonomy']) && $isPost) {
						if (!isset($item->_terms)) {
							//echo '<td></td>';
						} else {
							$activeTaxonomy = array_keys($this->postOptions['taxonomy']);
							$termLinks = '';
							
							echo '<td>';
							
							foreach ($item->_terms as $term) {
								if (!in_array($term->taxonomy, $activeTaxonomy)) {
									continue;
								}
								
								$termLinks .= '<a href="'. url('/') . '/' . preg_replace('~%.*%~', $term->taxonomy, $this->postOptions['rewrite']['slug']) . '/' . $term->slug . '/">'.$term->name.'</a>, ';
							}
							
							echo substr($termLinks, 0, -2) . '</td>';
						}
					}
					
					if (isset($item->add_keys)) {
						foreach ($item->add_keys as $value) {
							echo '<td>'.$value.'</td>';
						}
					}
				?>
				<td><?=($isPost ? $item->created_at : $item->count);?></td>
			</tr>
		<?php
		return ob_get_clean();
	}
	
	public function getCreateData()
	{
		$post = [];
		if($this->postOptions['hierarchical']){
			$posts = $this->getAllPosts(['id', 'parent', 'title', 'short_title', 'slug'], $this->postOptions['type']);
			$post['listForParents1'] = $this->listForParents($posts);
			$post['templates'] 		= $this->htmlSelectForTemplateList();
		}elseif(isset($this->postOptions['taxonomy']) && $this->postOptions['taxonomy']){
			$post['terms'] = $this->getTermList(array_keys($this->postOptions['taxonomy']));
			$post['terms'] = $this->hierarchyItems($post['terms']);
		}
		
		$post['__model'] = $this;
		
		$post['extra_fields_list'] = $this->getExtraFieldsOptions();
		
		return $post;
	}
	
	public function getTermList($taxonomy)
	{
		$terms =  \DB::select('Select DISTINCT t.*, tt.* from terms t, term_taxonomy tt where t.id = tt.term_id and tt.taxonomy IN(\'' . implode("','", (array)$taxonomy) . '\') order by t.id ASC');
		return $terms;
	}
	
	private function getExtraFieldsOptions(){
		$extra_fields_list = \Options::get('extra_fields');
		$extra_fields_list = $extra_fields_list ? unserialize($extra_fields_list) : false;
		return isset($extra_fields_list[$this->postOptions['type']]) ?
										$extra_fields_list[$this->postOptions['type']]:
										false;
	}
	
	
	private function htmlSelectForTemplateList($postTeplate = NULL)
	{
		$templateList = '';
		
		foreach (glob(themeDir('front') . '*.php') as $themeFile) {
			if (preg_match(self::TEMPLATE, file_get_contents($themeFile), $matches)) {
				$templateFile = basename($themeFile);
				$selected = $templateFile === $postTeplate || $templateFile === ($postTeplate . '.blade.php') ? ' selected' : '';
				$templateList .=  "<option value=\"{$templateFile}\"{$selected}>{$matches[1]}</option>";
			}
		}
		
		return !$templateList ? false : '<select style="width: 100%;" name="_jmp_post_template"><option value="0">(Базовый)</option>' . $templateList . '</select>';
	}
	
	public function listForParents($posts = NULL, $parent = NULL, $onlyData = false)
	{
		if (!isset($this->postOptions)) {
			$this->postOptions = PostsTypes::get(null, 'page');
		}
		
		if (!$posts) {
			$posts = $this->getAllPosts(['id', 'parent', 'title', 'short_title', 'slug']);
		}
			
		$itemsToParents = $this->hierarchyItems($posts);
		
		if ($onlyData) {
			return $itemsToParents;
		}
		
		return $this->htmlSelectForParentHierarchy($this->hierarchy($itemsToParents, 'select', $parent));
	}
	
	private function htmlSelectForParentHierarchy($hierarchyList){
		return '<select  autocomplete="off" style="width: 100%;" name="parent"><option value="0">(нет родительской)</option>' . $hierarchyList . '</select>';
	}
	
	
	// EDIT
	
	public function getEditData($id)
	{
		if (!$post = self::find($id)) {
			redir(route($this->postOptions['type'] . '.index'));
		}
		$post = $this->mergeMeta($post);
		
		if ($this->postOptions['hierarchical']) {
			$posts 					= $this->getAllPosts();
			$post->urlHierarchy 	= $this->getUrlHierarchy($posts, $post->id);
			$itemsToParents 		= $this->hierarchyItems($posts, $post->id);
			$post->listForParents1 = $this->htmlSelectForParentHierarchy($this->hierarchy($itemsToParents, 'select', $post->parent));
			$selfTemplate  			= isset($post->_jmp_post_template) ? $post->_jmp_post_template : false;
			$post->templates 		= $this->htmlSelectForTemplateList($selfTemplate);
			$post->anchor 			= url('/') . '/' . $post->urlHierarchy;
			$post->permalink 		= $post->anchor . $post->slug . '/';
		} else {
			$termsByPostId = $this->getTermsByPostsId($id)[$id];
			
			// terms id of this post for checkbox checked
			if ($termsByPostId) {
				foreach ($termsByPostId as $t) {
					$selfTerms[] = $t->term_id;
				}
				
				$post->selfTerms = $selfTerms;
			}
			
			$post->terms 		= $this->taxonomy->getByTaxonomies();
			// Cache::set('allTerms', $post->terms);
			list($termsOnId, $termsOnParent) = Arr::itemsOnKeys($post->terms, ['id', 'parent']);
			$permalink 	 		= url('/') . '/' . trim($this->postOptions['rewrite']['slug'], '/') . '/' . $post->slug . '/';
			$post->permalink 	= applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
			$post->anchor 		= str_replace($post->slug . '/', '', $post->permalink);
			
			if ($post->terms) {
				$post->terms 	= $this->hierarchyItems($post->terms);
			}
		}
		
		$post->extra_fields_list = $this->getExtraFieldsOptions();
		
		return $post;
	}
	
	public function termList($taxonomy)
	{
		$terms = $this->getTermList($taxonomy);
		$termsHierarchy = $this->hierarchyItems($terms);
		$termsTable = '<table class="mytable"><tr align="center"><td>Заголовок</td><td width="1%">Кол-во</td></tr>' . $this->hierarchy($termsHierarchy, 'table') . '</table>';
		return $termsTable;
	}
	
	public function showTermHierarchy($terms, $taxonomy, $selfTerms, $level = 0)
	{
		foreach ($terms as $key => $termData) {
			if($termData->taxonomy != $taxonomy) {
				continue;
			}
			$checked = in_array($termData->id, $selfTerms) ? 'checked' : '';
			echo str_repeat('&nbsp;', $level * 5);
			?>
				<label><input type="checkbox" name="terms[<?=$taxonomy?>][]" value="<?=$termData->id?>" <?=$checked?> /> <?=$termData->name?></label><br>
			<?php 
			if (isset($termData->children)) {
				$this->showTermHierarchy($termData->children, $taxonomy, $selfTerms, $level + 1);
			}
			unset($terms[$key]);
		}
		
		return $terms;
	}
	
	public function editTermForm($id)
	{
		$term = \DB::select('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and t.id = ?', [$id])[0];
		$itemsToParents = $this->hierarchyItems($this->getAllTerms($term->taxonomy), $term->id);
		$data['listForParents'] = $this->htmlSelectForParentHierarchy($this->hierarchy($itemsToParents, 'select', $term->parent));
		$data['term'] = $term;
		
		return $data;
		
	}
	
	public function getAllTerms($taxonomies){
		if (!is_array($taxonomies)) {
			$taxonomies = [$taxonomies];
		}
		$taxonomies = implode("','", $taxonomies);
		
		if(!isset($this->allTerms[$taxonomies])){
			// $this->allTerms[$taxonomies] = \DB::select('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_taxonomy_id and tt.taxonomy IN(\''.$taxonomies.'\')');
			$this->s('allTerms.' . $taxonomies, DB::select('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_taxonomy_id and tt.taxonomy IN(\''.$taxonomies.'\')'));
		}
		
		return $this->allTerms[$taxonomies];
	}
	
	
	// public function addTerm($name, $term, $slug = '', $description = '', $parent = 0)
	public function addTerm($fields)
	{
		$fields = textSanitize($fields);
		
		if ($this->checkTermDuplicate($fields['taxonomy'], $fields['slug'])) {
			$errors[] = "Duplicate term '<b>{$fields['slug']}</b>' for <b>{$this->postOptions['type']}</b> taxonomy: <b>{$fields['taxonomy']}</b>";
		}
		
		if ($fields['parent'] != "0" && !$this->checkTermParentExists($fields['parent'])) {
			$errors[] = "Parent is Invalid";
		}
		
		if (isset($errors)) {
			rdr(':back', 302, $errors);
		}
		// dd($fields);
		if ($term = Term::create($fields)) {
			
			Taxonomy::create(array_merge($fields, ['term_id' => $term->id, 'count' => 0]));
			
			return $term->id;
		}
		
		return false;
	}
	
	private function checkTermDuplicate($taxonomy, $slug)
	{
		return selectRow('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and tt.taxonomy = ? and t.slug = ?', [$taxonomy, $slug]);
	}
	
	private function checkTermParentExists($parent)
	{
		return selectOne('Select t.id from terms t, term_taxonomy tt where t.id = tt.term_id and t.id = ?', [$parent]);
	}
	
	public function addTermForm($term)
	{
		$data['taxonomy'] 		= $data['add'] = $term;
		$itemsToParents 		= $this->hierarchyItems($this->getAllTerms($term));
		$data['listForParents'] = $this->htmlSelectForParentHierarchy($this->hierarchy($itemsToParents));
		
		return $data;
	}
	
	
	public function s($key, $value) 
	{
		// $keys = explode('.', $key);
		// $countKeys = count($keys);
		// if ($countKeys == 1) {
			// $this->attributes[$keys[0]] = $value;
		// } elseif ($countKeys == 2) {
			// $this->attributes[$keys[0]][$keys[1]] = $value;
		// } elseif ($countKeys == 3) {
			// $this->attributes[$keys[0]][$keys[1]][$keys[2]] = $value;
		// } elseif ($countKeys == 4) {
			// $this->attributes[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
		// }
		
		Arr::push($this->attributes, $key, $value);
	}
	
	
	public function getPostImg($post, $key)
	{
		if (isset($post[$key])) {//dd((int)$post[$key], Media::where( 'id', (int)$post[$key])->get(), Media::find((int)$post[$key]));
			// return \DB::select('Select * from media where id = ?', [(int)$post[$key]]);
			// return Media::first($post[$key]);
			return Media::where( 'id', (int)$post[$key])->first();
		}
	}
	
	public function mergeMeta($post, $mod = false)
	{
		if (!$meta = \App\PostMeta::where('post_id', $post->id)->get()) {
			return $post;
		}
		
		$metadata = [];
		
		foreach ($meta as $m) {
			if ($mod && $m['meta_key'] == '_jmp_post_img') {
				$media 						= Media::find($m['meta_value']);
				$m['meta_value'] 			= $media['src'];
				$post->_jmp_post_img_meta = unserialize($media['meta']);
				
			}
			$post[$m['meta_key']] = $m['meta_value'];
			if (mb_substr($m['meta_key'], 0, 1) == '_') continue;
			$metadata[$m['meta_key']] = $m['meta_value'];
		}
		
		$post->meta_data = $metadata;
		
		return $post;
	}
	
	public function getUrlHierarchy($posts, $childId)
	{
		foreach($posts as $post){
			$postsKeysId[$post->id] = $post;
		}
		$hierarchyUrl = '';
		if(isset($postsKeysId[$postsKeysId[$childId]['parent']]))
			$parent[] = $postsKeysId[$postsKeysId[$childId]['parent']];
		$i = 0;
		while(isset($parent[$i])){
			$hierarchyUrl .= '/' . $parent[$i]['url'];
			if($parent[$i]['parent']){
				$parent[] = $postsKeysId[$parent[$i]['parent']];
			}
			$i++;
		}
		return implode('/', array_reverse(explode('/', $hierarchyUrl)));
	}
	
	
	
	public function getAllPosts($columns = [], $postType = null)
	{
		$postType = $postType ?? $this->postOptions['type'];
		$key = empty($columns) ? '*' : implode(',', $columns); 
		if(!isset($this->allPosts[$postType][$key])){
			$distinct = false;
			$order = 'DESC';
			
			if (isset($_GET['order'])) {
				// if (in_array($_GET['order'], ['DESC', 'ASC', 'DISTINCT'], false)) {
					// if ($_GET['order'] == 'DISTINCT') 	{
						// if ($saveOrder = getPostOrderType($this->options['type'])) {
							// $distinct = true;
						// }
						
						// $order = 'ASC';
					// } else {
						// $order = $_GET['order'];
					// }
				// }
			} else {
				// if ($saveOrder = getPostOrderType($this->options['type'])) {
					// if ($saveOrder['order'] == 'DISTINCT') {
						// $distinct = true;
						// $order = 'ASC';
					// } else {
						// $order = $saveOrder['order'];
					// }
				// }
			}
			
			// dd($postType);
			$allPosts[$postType][$key] = self::select(...$columns)
						->where('post_type', $postType)
						->orderBy('id', $order)
						->get();
			
			if ($distinct) {
				foreach (explode(',', $saveOrder['value']) as $id) {
					$sortedPosts[$id] = false;
				}
				
				// Проверим кол-во элементов, если добавили пост или удалили - отследить
				//$preLength = count($sortedPosts);
				
				foreach ($allPosts[$postType][$key] as $post) {
					$sortedPosts[$post->id] = $post;
				}
				
				$allPosts[$postType][$key] = $sortedPosts;
				//dd($sortedPosts);
			}
		}
		
		$this->allPosts = $allPosts;
		
		return $this->allPosts[$postType][$key];
	}
}
