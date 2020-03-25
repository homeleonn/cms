@extends('layout')

@section('content')
<?php 
echo '<script>var menuItems = $.parseJSON(\'' . json_encode($data['menuItems']) . '\');</script>';
?>

<form method="POST">
	<div class="wrapper">
		<div>Создать новое меню</div>
		<div>
			<input type="text" name="new_menu">
			<button>Создать</button>
		</div>
	</div>
</form>

<?php if($data['menus']):?>
<div class="wrapper">
	<div>Выбрать меню</div>
	<div class="menu-select">
		<select name="menu" id="menu-select">
			<?php
				foreach($data['menus']['list'] as $menu){
					echo '<option value="',$menu['id'],'" ',($data['menus']['id'] == $menu['id'] ? 'selected':''),'>',$menu['name'],'</option>';
				}
			?>
		</select>
		<button>Сделать активным</button>
		<form method="POST" style="display: inline;">
			<input type="hidden" id="del_menu_id" name="del_menu_id">
			<button>Удалить выбранное меню</button>
		</form>
	</div>
</div>
<?php endif;?>
<textarea id="nestable-output" style="width: 100%; display: none;"></textarea>

<link rel="stylesheet" type="text/css" href="/admin_static/css/menu.css">
<div id="menu-create">
	<div class="inset">
		<?php
			$i = 0;
			foreach($data['types'] as $key => $type){
				if($key == 'taxonomies'){
					foreach($type as $tt=>$t){
						$active = !$i ? 'class="active"' : '';
						echo '<div ', $active,' data-id="',$i ,'">', $t['title'],'</div>';
						$i++;
					}
				}else{
					$active = !$i ? 'class="active"' : '';
					echo '<div ', $active,' data-id="',$i ,'">', $type['title_for_admin'],'</div>';
					$i++;
				}
				
			}
		?>
		<div data-id="<?=$i?>">Произвольная ссылка</div>
	</div>
	<div class="item-lists">
	<?php
			$i = 0;
			foreach($data['types'] as $key => $type){
				if($key == 'taxonomies'){
					
					foreach($type as $tt => $t){
						$posts = $data['posts_on_types']['taxonomies'][$tt];
						$active = !$i++ ? 'class="active"' : '';
						echo '<div ', $active,'>';
						
						menuRecursive1($posts, ($t['archive'] ? $t['archive'] . '/' : '') . $tt);
							
						echo '</div>';
					}
					continue;
				}
				$posts = $data['posts_on_types'][$type['type']];
				$active = !$i++ ? 'class="active"' : '';
				echo '<div ', $active,'>';
				if($type['hierarchical']){
					menuRecursive($posts);
				}else{
					foreach($posts as $page){
						echo '<input type="checkbox" data-name="',$page['title'],'" data-url="', $page['slug'],'" data-origname="',$page['title'],'" data-type="Страница" > <a href="',$page['slug'],'">',$page['title'],'</a>', "<br>\n";
					}
				}
					
				echo '</div>';
			}
			function menuRecursive($pages, $url = '', $level = 0){
				foreach($pages as $page)
				{
					echo ' ', str_repeat('---', $level), ' <input type="checkbox" data-name="',$page['title'],'" data-url="', url('/'), $page['slug'],'" data-origname="',$page['title'],'" data-type="Страница" > <a href="',url('/'), $page['slug'],'/">',$page['title'],'</a>', "<br>\n";
					
					if(isset($page['children']))
						menuRecursive($page['children'], $url . $page['slug'] . '/', $level + 1);
				}
			}
			
			function menuRecursive1($pages, $taxonomy, $url = '', $level = 0){
				foreach($pages as $page)
				{
					$page = (array)$page;
					echo ' ', str_repeat('---', $level), ' <input type="checkbox" data-name="',$page['name'],'" data-url="', $taxonomy, '/',$url, $page['slug'],'" data-origname="',$page['name'],'" data-type="Страница" > <a href="',url('/'), $taxonomy, '/', $url, $page['slug'],'/">',$page['name'],'</a>', "<br>\n";
					
					if(isset($page['children']))
						menuRecursive1($page['children'], $taxonomy, $url . $page['slug'] . '/', $level + 1);
				}
			}
			
		?>
		<div id="some-link">
			Имя<br>
			<input class="name"></input><br><br>
			Ссылка<br>
			<input class="url"></input><br>
			<button>Добавить</button>
		</div>	
	</div>
	<div style="padding: 5px;" class="tools ">
		<span class="link">Выделить все</span>
		<button>Добавить в меню</button>
	</div>
</div>

<div class="dd" id="nestable3">
    <ol class="dd-list">
		
	</ol>
</div>
<button id="save-menu" class="none">Сохранить</button>

<div class="none" id="item-prototype">
	<li class="dd-item dd3-item" data-id="13">
		<div class="dd-handle dd3-handle"></div>
		<div class="dd3-content">
			<span class="item-title">
				<span class="new">Item 13</span>
				<span class="old">Item 13</span>
			</span>
			<div class="trigger"><span> &#9660; </span><span> &#9650; </span></div>
			<div class="sub">
				<hr>
				<div>Текст ссылки<br><input type="text" class="linkname" value="Item 13" /></div>
				<div>Оригинал: <a href="#" class="original-link">Item 13</a></div>
				<div>
					<a href="javascript:void(0);" class="red del">Удалить</a> |
					<a href="javascript:void(0);" class="cancel">Отмена</a>
				</div>
			</div>
		</div>
	</li>
</div>
@endsection