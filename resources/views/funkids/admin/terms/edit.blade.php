@extends('layout')

@section('content')
<?php  
// dd(get_defined_vars());
if(isset($_GET['msg']))
	echo "<h3 style='padding: 10px;background: lightgreen;'>{$_GET['msg']}</h3>";
$term = $data['term'];
$post['listForParents1'] = $data['listForParents'];
?>
<h2><?=$postOptions['taxonomy'][$term->taxonomy]['edit']?></h2>
@include('layouts.errors')
{{ 
	Form::open([
		'route' 		=> [$postOptions["type"] . '.term_update', $term->id], 
		'class' 		=> 'post-from-admin', 
		'autocomplete' 	=> 'off', 
		'id' 			=> 'edit-term-' . $postOptions['type'], 
		'method' 		=> 'PUT'
	]) 
}}
	<div id="center" class="col-md-8">
		<input type="hidden" name="id" value="<?=$term->id?>">
		<input type="hidden" name="taxonomy" value="<?=$term->taxonomy?>">
		<div class="block1">
			<div>Имя</div>
			<div><input class="w100" type="text" name="name" id="name" value="<?=$term->name?>" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Slug</div>
			<div><input class="w100" type="text" name="slug" id="slug" value="<?=$term->slug?>" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Описание</div>
			<div><textarea class="nonEditor" name="description" id="description" value="" style="width: 100%;height: 600px;"><?=$term->description?></textarea></div>
		</div>
	</div>
	<div id="sidebar-right" class="col-md-4">
		<br><br><input type="submit" id="item-factory" value="Редактировать">
		@include('posts.sidebar.listForParents')
	</div>
	
	<div class="sep"></div>
	
{{ Form::close() }}
@endsection