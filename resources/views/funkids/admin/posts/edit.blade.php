@extends('layout')

@section('content')
	@include('layouts.errors')
	
	<?php  
		// dd(get_defined_vars());
		// echo doAction('admin_post_options_form');
	?>
	<h1>{{$postOptions['add'] ?? $postOptions['title'] . ' edit'}}</h1>
	<a href="{{ route($postOptions['type'] . '.create') }}" class="action-tool plus" title="Добавить">
		<span class="icon-plus">Добавить</span>
	</a>
	{{ 
		Form::open([
			'route' 		=> [$postOptions["type"] . '.update', $post['id']], 
			'files' 		=> true, 
			'class' 		=> 'post-from-admin', 
			'autocomplete' 	=> 'off', 
			'id' 			=> 'edit-' . $postOptions["type"], 
			'method' 		=> 'PUT'
		]) 
	}}
		<div id="center" class="col-md-8">
			<input type="hidden" name="id" id="post_id" value="<?=$post['id']?>">
			<input type="hidden" name="slug" value="<?=$post['slug']?>">
			<div class="block1">
				<div>Заголовок</div>
				<div><input class="w100" value="<?=$post['title']?>" type="text" name="title" id="title" placeholder=""></div>
				<div>
					<a id="url" href="<?=$post['permalink']?>"><span class="anchor"><?=$post['anchor']?></span><span class="editing-part"><?=$post['slug']?></span><span id="url-end">/</span></a> 
					<input type="button" id="edit-url-init" value="Изменить" style="padding: 0px 4px;">
					<input type="button" id="edit-url-ok" value="ок" style="padding: 0px 4px; display: none;">
					<input type="button" id="edit-url-cancel" value="отмена" style="padding: 0px 4px; display: none;">
				</div>
			</div>
			
			<div class="block1">
				<div>Краткий заголовок <span class="icon-help-circled" title="хлебные крошки, меню. Если главный заголовок имеет небольшую длинну, данное поле можно не заполнять"></span></div>
				<div><input class="w100" value="<?=$post['short_title']?>" type="text" name="short_title" id="short_title" placeholder=""></div>
			</div>
			
			<div class="block1">
				<div>Текст</div>
			<input type="hidden" name="content" value="">
				<div id="editors"><textarea class="visual" id="content" value="1" style="width: 100%;height: 600px;display: none; visibility:hidden;"><?=htmlspecialchars($post['content'])?></textarea></div>
			</div>
			<?php doAction('edit_post_after', $post, $postOptions);?>
			@include('posts.sidebar.comments')
			@include('posts.sidebar.extraFields')
			<?=doAction('add_extra_rows', $postOptions['type']);?>
		</div>
		<div id="sidebar-right" class="col-md-4">
			<br><br><span class="icon-calendar"></span> Добавлено: <?=$post['created_at']?>
			<br><span class="icon-pencil"></span> Последнее редактирование: <?=$post['updated_at']?>
			<br><br><input type="submit" value="Редактировать">
				
			@include('posts.sidebar.categoriesAndTags')
			@include('posts.sidebar.listForParents')
			@include('posts.sidebar.discussion')
			@include('posts.sidebar.image')
		</div>
	{{ Form::close() }}
@include('posts.sidebar.extra-field-prototype')
@endsection
