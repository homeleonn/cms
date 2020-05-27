<?php

namespace App\Helpers;

trait Html
{
	public function createLink($post)
	{
		if (!$this->postOptions['hierarchical']) {
			if (!empty($terms = Arr::itemsOnKeys($this->_allTerms, ['id', 'parent']))) {
				[$termsOnId, $termsOnParent] = $terms;
				
				if (isset($this->_postTerms['_terms'][$item->id])) {
					$termsByPostId = $this->_postTerms['_terms'][$item->id];
					$permalink 	 = url('/') . '/' . trim($this->postOptions['rewrite']['slug'], '/') . '/' . $item->slug . '/';
					$item->slug = applyFilter('postTypeLink', $permalink, $termsOnId, $termsOnParent, $termsByPostId);
					$buildLinkFlag = true;
				}
			}
		}
	}
	
	private function hierarchyListHtml1($item, $level, $urlHierarchy)
	{
		// d($item);
		$isPost = !isset($item->term_taxonomy_id);
		$buildLinkFlag = false;
		
		
		if ($isPost) {
			//$url = $item->slug;
			// dd($urlHierarchy);
			$url = ($this->postOptions['hierarchical'] ? url('/') . '/' . ($urlHierarchy ? $urlHierarchy  . '/' : '') . $this->postOptions['has_archive'] . $item->slug . '/' : ($buildLinkFlag ? $item->slug : '/' . $this->postOptions['has_archive'] . '/' . $item->slug . '/'));
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
							
							echo rtrim($termLinks, ',') . '</td>';
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
	
	
	private function hierarchy1($items, $type = 'select', $parent = 0, $level = 0, $urlHierarchy = ''){
		$html = '';
		
		foreach ($items as $item) {
			if ($type == 'select') {
				$title = isset($item->title) ? $item->title : $item->name;
				$html .= '<option '.($item->id == $parent ? 'selected' : '').' value="' . $item->id . '">' . str_repeat('&nbsp;', $level * 3) . ($level ? '&#8735;'  : '') . (mb_strlen($title) > 46 ? mb_substr($title, 0, 45) . '...' : $title) . '</option>';
			} elseif($type = 'table') {
				$html .= $this->hierarchyListHtml1($item, $level, $urlHierarchy);
			}
			
			if (isset($item->children)) {
				$urlHierarchy .= (isset($item->slug) ? $item->slug : $item->slug) . '/';
				$html .= $this->hierarchy($item->children, $type, $parent, $level + 1, $urlHierarchy);
			}
			
			if (!$item->parent) {
				$urlHierarchy = '';
			}
		}
		
		return $html;
	}
}