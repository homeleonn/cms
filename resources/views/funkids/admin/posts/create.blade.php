@extends('layout')

@section('content')
	
@include('layouts.errors')
	
<?//=dd(get_defined_vars())?>
<?//=doAction('admin_post_options_form');?>
<h2>{{$postOptions['add'] ?? $postOptions['title'] . ' add'}}</h2>
{{ 
	Form::open([
		'route' 		=> $postOptions["type"] . '.store', 
		'files' 		=> true, 
		'class' 		=> 'post-from-admin', 
		'autocomplete' 	=> 'off', 
		'id' 			=> 'add-' . $postOptions["type"], 
		'method' 		=> 'POST'
	]) 
}}
	<div id="center" class="col-md-8">
		<input type="hidden" name="id">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" type="text" name="title" id="title" placeholder="" value="{{ old('title') }}"></div>
		</div>
		<div class="block1">
			<div>Краткий заголовок <span class="icon-help-circled" title="хлебные крошки, меню. Если главный заголовок имеет небольшую длинну, данное поле можно не заполнять"></span></div>
			<div><input class="w100" type="text" name="short_title" id="short_title" placeholder="" value="{{ old('short_title') }}"></div>
		</div>
		<div class="block1">
			<div>Текст</div>
			<div id="editors"><textarea class="visual" name="content" id="content" value="" style="width: 100%;height: 600px;display: none; visibility:hidden;">{{ old('content') }}</textarea></div>
		</div>
		<?php doAction('add_post_after', $post);?>
		@include('posts.sidebar.comments')
		@include('posts.sidebar.extraFields')
		<?=doAction('add_extra_rows', $postOptions['type']);?>
	</div>
	
	<!-- Block for add post categories -->
	<!-- Block for add post tags -->
	<div id="sidebar-right" class="col-md-4">
		<input type="submit" value="Добавить">
		
		@include('posts.sidebar.categoriesAndTags')
		@include('posts.sidebar.listForParents')
		@include('posts.sidebar.discussion')
		@include('posts.sidebar.image')
	</div>
	
	<div class="sep"></div>
{{ Form::close() }}
@include('posts.sidebar.extra-field-prototype')
@endsection
