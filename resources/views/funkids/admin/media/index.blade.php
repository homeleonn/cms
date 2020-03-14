@extends('layout')
<?php

//var_dump(get_defined_vars());
// var_dump($data);
?>
@section('content')
<form method="POST" enctype="multipart/form-data">
	<label>
		<span class="icon-plus add-img green s18">
			<input class="none-impt" multiple type="file" accept="image/jpeg,image/png,image/gif" id="upload-img">
		</span>
	</label>
	<span>Максимальный размер файла: <?=ini_get('upload_max_filesize')?>B</span>
</form>
<div class="row" style="margin-left: 10px;">
	<div class="media-thumbs">
	<?php 
	foreach($data['media'] as $media):
		$meta = unserialize($media['meta']);
		if(isset($meta['sizes']['thumbnail'])){
			$img = $meta['dir'] . $meta['sizes']['thumbnail']['file'];
			unset($meta['sizes']['thumbnail']);
		}else{
			$img = $media['src'];
		}
		$img = uploads_url() . $img; 
		
		$orig = uploads_url() . $media['src'];
	?>
		<div class="media-thumb"><img src="<?=$img;?>" data-original="<?=$orig?>" data-id="<?=$media['id']?>" data-meta='<?=json_encode($meta)?>' data-dir="<?=uploads_url() . $meta['dir'];?>"></div>
	<?php endforeach;?>
	</div>
	<div id="wrap-media" >
		<div id="media-original-show" class=" none">
			<img src="" class="shower">
			<div class="padd5 size b"></div>
			<div class="padd5">Путь:<input type="text" class="w100"></div>
			<span class="icon-pencil"></span>
			<span class="icon-cancel media-delete" id="media-delete"></span><br>
			<div class="btn none padd10" id="select-for-post">Выбрать</div>
			<div class="thumbnails"></div>
		</div>
	</div>
</div>
@endsection