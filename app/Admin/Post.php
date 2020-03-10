<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\{Arr};

class Post extends Model
{
	public function __construct()
	{
		$this->taxonomy = new \App\Taxonomy;
	}
    public function list()
	{
		if (!$posts = $this->getAllPosts()) abort(404);
		$addKeys = [];
		if(!$this->postOptions['hierarchical']){
			// Get posts terms
			foreach($posts as $post) $ids[] = $post['id'];
			// $addKeys['_terms'] = $this->getTermsByPostsId($ids);
			$this->_postTerms = $this->getTermsByPostsId($ids);
			$this->_allTerms = $this->taxonomy->getByTaxonomies();
			// Cache::set('postTerms', $addKeys['_terms']);
			
			// Get all terms for post taxonomies for cache(build link)
			// Cache::set('allTerms', $this->taxonomy->getByTaxonomies());
		}
		
		
		// build hierarchy
		$postsHierarchy = $this->hierarchyItems($posts, NULL, NULL, $addKeys);
		
		$postsTable = $this->hierarchy($postsHierarchy, 'table');
		// dd($postsTable);
		return !$postsTable ? '' : '<table class="mytable posts '.$this->postOptions['type'].'" id="draggable"><tr align="center"><td>title/url</td>'.(($this->postOptions['taxonomy'] ?? false) ? '<td width="15%">Метки</td>' : '').'<td width="1%">Дата публикации</td></tr>' . $postsTable . '</table>';
		
		
	}
	
	public function getTermsByPostsId($ids)
	{
		$ids = is_array($ids) ? implode(',', $ids) : $ids;
		if(!$terms = \DB::select('Select t.*, tt.*, tr.object_id from terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tr.term_taxonomy_id = tt.term_taxonomy_id and object_id IN('.$ids.') order by t.id ASC')) return false;
		foreach($terms as $t){
			$termsByObject[$t['object_id']][] = $t;
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
		if(empty($items)){
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
				$title = isset($item['title']) ? $item['title'] : $item['slug'];
				$html .= '<option '.($item['id'] == $parent ? 'selected' : '').' value="' . $item['id'] . '">' . str_repeat('&nbsp;', $level * 3) . ($level ? '&#8735;'  : '') . (mb_strlen($title) > 46 ? mb_substr($title, 0, 45) . '...' : $title) . '</option>';
			}elseif($type = 'table'){
				$html .= $this->hierarchyListHtml($item, $level, $urlHierarchy);
			}
			
			if(isset($item['children'])){
				$urlHierarchy .= (isset($item['url']) ? $item['url'] : $item['slug']) . '/';
				$html .= $this->hierarchy($item['children'], $type, $parent, $level + 1, $urlHierarchy);
			}
			
			if(!$item['parent'])
				$urlHierarchy = '';
		}
		return $html;
	}
	
	private function hierarchyListHtml($item, $level, $urlHierarchy){
		$isPost = !isset($item['taxonomy']);
		$buildLinkFlag = false;
		if($isPost && !$this->postOptions['hierarchical']){
			if(!empty($terms = Arr::itemsOnKeys($this->_allTerms, ['id', 'parent']))){
				list($termsOnId, $termsOnParent) = $terms;
				$termsByPostId = $this->_postTerms[$item['id']];
				$permalink 	 = url('/') . trim($this->postOptions['rewrite']['slug'], '/') . '/' . $item['url'] . '/';
				$item['url'] = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
				$buildLinkFlag = true;
			}
		}
		
		if($isPost){
			//$url = $item['url'];
			$url = ($this->postOptions['hierarchical'] ? '/' . $urlHierarchy . $this->postOptions['has_archive'] . $item['url'] . '/' : ($buildLinkFlag ? $item['url'] : '/' . $this->postOptions['has_archive'] . $item['url'] . '/'));
		}else{
			$url = '/' . $this->postOptions['has_archive'] . $item['taxonomy'] . '/' . $urlHierarchy . $item['slug'] . '/' ;
		}
		
		
		$link = '<a href="' . $url . '">перейти</a>';
		$edit = '<a href="' . url('/') . '/admin/' . $this->postOptions['type'] . '/' . ($isPost ? 'edit' : 'edit-term') . '/' . $item['id'] . '/">%s</a>';
		ob_start();
		?>
			<tr data-post_id="<?=$item['id']?>">
				<td class="admin-page-list">
					<?=str_repeat('&mdash;', $level) . ' ' . sprintf($edit, $item[$isPost ? ($item['short_title']?'short_title':'title') : 'name']);?>
					<div style="position: absolute;">
						[<?=$link;?>]
						[<a href="#">свойства</a>]
						[<?=sprintf($edit, 'изменить');?>]
						[<a style="color: red;" href="javascript:void(0);" title="<?=$this->postOptions['delete'] ?? 'Delete'?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$this->postOptions['type']?>',<?=$item['id'];?>, '<?=($isPost ? 'post' : 'term')?>');">удалить</span></a>]
					</div>
				</td>
				<?php 
					if($this->postOptions['taxonomy'] ?? false && $isPost){
						if(!isset($item['_terms'])) echo '<td></td>';
						else{
							$activeTaxonomy = array_keys($this->postOptions['taxonomy']);
							echo '<td>';ob_start();
							foreach($item['_terms'] as $term){
								if(!in_array($term['taxonomy'], $activeTaxonomy)) continue;
								echo '<a href="'. url('/') . '/' . $this->postOptions['rewrite']['slug'] . '/' . $term['taxonomy'] . '/' . $term['slug'] . '/">'.$term['name'].'</a>, ';
							}
							echo substr(ob_get_clean(), 0, -2) . '</td>';
						}
					}
					if(isset($item['add_keys'])){
						foreach($item['add_keys'] as $value)
							echo '<td>'.$value.'</td>';
					}
				?>
				<td><?=($isPost ? $item['created'] : $item['count']);?></td>
			</tr>
		<?php
		return ob_get_clean();
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
					$sortedPosts[$post['id']] = $post;
				}
				
				$allPosts[$postType][$key] = $sortedPosts;
				//dd($sortedPosts);
			}
		}
		
		$this->allPosts = $allPosts;
		
		return $this->allPosts[$postType][$key];
	}
}
