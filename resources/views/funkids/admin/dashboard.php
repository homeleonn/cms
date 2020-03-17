<?php

function getSections($type, $pageTypes){
	if($type == 'production'){
		$sections = ['Главная панель||gauge' => ''];
		$sections['Медиафайлы||picture'] = 'media';
		if($pageTypes){
			foreach($pageTypes as $pt){
				$title = $pt['title_for_admin'] ?? $pt['title'];
				$key = ($title) . '||' . ($pt['icon'] ?? 'cog');
				$sections[$key] = [
					'Все ' . mb_strtolower($title) => $pt['type'],
					'Добавить новую' => $pt['type'] . '/create',
				];
				if(!empty($pt['taxonomy'])){
					$ptTaxes = [];
					foreach($pt['taxonomy'] as $tax => $values){
						$tempTax = [$values['title'] => $pt['type'] . '/term/?taxonomy=' . $tax];
						if(empty($ptTaxes)){
							$ptTaxes = $tempTax;
							continue;
						}
						$ptTaxes = array_merge($ptTaxes, $tempTax);
					}
					$sections[$key] = array_merge($sections[$key], $ptTaxes);
				}
			}
		}
		$sections = array_merge($sections, 
			//['Комментарии||comment' => 'comments'],
			//['Пользователи||user' => 'users'],
			['Плагины||cog' => 'plugins'],
			['Настройки||cog' => [
					'Общие' => 'settings',
					'Меню' => 'menu',
				]
			]
		);
	}else{
		$sections = [
			'Главная панель||gauge' => '',
			'Страницы||tags' => [
				'Все страницы' => 'page',
				'Добавить новую' => 'page/add',
			],
			'Комментарии||comment' => '',
			'Пользователи||user' => '',
			'Настройки||cog' => [
				'Меню' => 'menu',
			],
		];
	}
	
	$sections = applyFilter('edit_admin_menu', $sections);
	
	// echo "\n".'$sections = [';
	// foreach($sections as $key => $s){
		// echo "\n\t".'\''.$key . '\' => ';
		// if(is_array($s)){
			// echo "[\n\t";
			// foreach($s as $k => $ss){
				// echo "\t" . '\''.$k.'\' => \''.$ss."',\n\t";
			// }
			// echo "],";
		// }else{
			// echo '\''.$s."',";
		// }
	// }
	// echo "\n];";
	// exit;
	return $sections;
}

$type = /*local*/'production'/**/;
$pageTypes = NULL;
if($type == 'production'){
	$pageTypes = App\Helpers\PostsTypes::getAll();
}
$sections = getSections($type, $pageTypes);

function menu1($menu, $title = 0, $stage = 0){
	$menuStr = '';
	$selfStage = $stage + 1;
	if($stage){
		if(!is_numeric($title)) 
			$menuStr .= drawLink(false, $title)."\n";
		$menuStr .= '<ul class="submenu'.$stage.'">'."\n";
	}else{
		$menuStr .= '<ul id="menu">'."\n";
	}
	
	foreach($menu as $key => $val){
		if(!is_array($val)){
			$menuStr .= !$stage ? '<li class="top">':'';
			$parent = $stage ? false : 2;
			$menuStr .= drawLink($val, $key, $parent)."\n";
			$menuStr .= !$stage ? '</li>':'';
		}else{
			$menuStr .= '<li class="top">' . menu1($val, $key, $selfStage) . '<li>';
		}
	}
	$menuStr .=  '</ul>';
	
	return $menuStr;
}

function drawLink($path, $name, $parent = 1){
	if($path && strpos($path, '?') === false) $path .= '/';
	$path = $path !== false ? url('/').'/admin/'.$path : 'javascript:void(0);';
	
	if($parent){
		$name = explode('||', $name);
		$name[1] = !isset($name[1]) ? '' : 'class="icon-'.$name[1].'"';
		$supParent = ($parent == 1) ? '<span></span>' : '';
	}else{
		$name = explode('|', $name);
		$add = (isset($name[1]) ? $name[1] : '');
		$name = $name[0];
	}
	
	return $parent ? '<div class="title"><a href="'.$path.'" '.$name[1].' data-menu="1">'.$name[0].$supParent.'</a></div>'
					 : '<li>'.($add ? '<a class="add icon-plus" href="'.$path.$add.'"></a>':'').'<a href="'.$path.'">'.$name.'</a><div class="clearfix"></div></li>' ;
}

echo menu1($sections);