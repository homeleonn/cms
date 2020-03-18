@extends('layout')

@section('content')
<?php  
// d(get_defined_vars(), $app);
if(isset($_GET['msg']))
	echo "<h3 style='padding: 10px;background: lightgreen;'>{$_GET['msg']}</h3>";
$post['listForParents1'] = $data['listForParents'];
?>
<h2><?=$postOptions['taxonomy'][$data['taxonomy']]['add']?></h2>
@include('layouts.errors')
{{ 
	Form::open([
		'route' 		=> $postOptions["type"] . '.term_store', 
		'class' 		=> 'post-from-admin', 
		'autocomplete' 	=> 'off', 
		'id' 			=> 'add-term', 
		'method' 		=> 'POST'
	]) 
}}
	<div id="center" class="col-md-8">
		<input type="hidden" name="taxonomy" value="<?=$data['taxonomy']?>">
		<div class="block1">
			<div>Имя</div>
			<div><input class="w100" type="text" name="name" id="name" placeholder="" value=""></div>
		</div>
		
		<div class="block1">
			<div>Slug</div>
			<div><input class="w100" type="text" name="slug" id="slug" placeholder="" value=""></div>
		</div>
		
		<div class="block1">
			<div>Описание</div>
			<div><textarea class="nonEditor" name="description" id="description" value="1" style="width: 100%;height: 200px;"></textarea></div>
		</div>
	</div>
	<div id="sidebar-right" class="col-md-4">
		<!--<input type="button" id="item-factory" value="Добавить">-->
		<input type="submit" id="" value="Добавить">
		@include('posts.sidebar.listForParents')
	</div>
	
	<div class="sep"></div>
{{ Form::close() }}
@endsection