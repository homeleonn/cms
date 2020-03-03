<?php

/*
 * Plugin Name: Localization (l10n)
 * Plugin URI: 
 * Description: Локализация записей
 * Version: 0.1
 * Author: Anonymous
 * Author URI: 
 * License: 
 */
 
namespace l10n;

class Localization{
	public function form(){
		global $post;
		$l10n_en = isset($post['l10n_en']) ? $post['l10n_en'] : '';
		?>
		<div id="post-l10n" class="side-block">
			<div class="block-title">Локализация</div>
			<div class="block-content">
				EN:<br>
				<textarea name="l10n_en" id="l10n_en" style="width: 100%; height: 300px;"><?=$l10n_en?></textarea>
			</div>
		</div>
		<?php
	}
	
	public function addFields($extraFieldKeys){
		$extraFieldKeys = array_merge(
			$extraFieldKeys, 
			['l10n_en']
		);
		
		return $extraFieldKeys;
	}
	
	public function setContent($post){
		if(LANG != 'ru'){
			$single = false;
			
			if(isset($post['id'])) {
				$post = [$post];
				$single = true;
			}
			
			foreach($post as &$p){
				if(isset($p['l10n_' . LANG]))
					$p['content'] = $p['l10n_' . LANG];
			}
			
			if($single)
				$post = $post[0];
		}
		
		return $post;
	}
	
	public function adminMenuAdd($sections){
		// $sections = array_merge($sections, 
			// ['Локализация' => ]
		// );
	}
	
	public function settings($page){
		$existingPages = ['l10n' => 'index'];
		if(isset($existingPages[$page]) && file_exists($filename = __DIR__ . '/templates/' . $existingPages[$page] . '.php')){
			$l10n = getOption('l10n', true);
			$defaultLang = isset($l10n['default_lang']) ? $l10n['default_lang'] : '';
			include $filename;
		}
	}
}

$l10n = new Localization();
addAction('add_post_after', [$l10n, 'form']);
addAction('edit_post_after', [$l10n, 'form']);
addAction('admin_page', [$l10n, 'settings']);
addFilter('extra_fields_keys', [$l10n, 'addFields']);
addFilter('before_return_post', [$l10n, 'setContent']);