<?php
$img = isset($data['_jmp_post_img']) ? $data['_jmp_post_img'] : false;
$src = $id = $none = $del = '';
if($img){
	$src = UPLOADS.$img['src'];
	$id  = $img['id'];
}else{
	$none  = 'none';
	$del = 'none';
}
?>
<div id="post-images" class="side-block">
	<div class="block-title">Изображение страницы</div>
	<div class="block-content">
		<span class="icon-plus" id="add-post-img"></span>
		<span class="icon-cancel red cancel <?=$del?>"></span>
		<div id="post-img-container" class="<?=$none?>"><img src="<?=$src?>" class="shower"></div>
		<input class="none-impt" type="hidden" name="_jmp_post_img" value="<?=$id?>">
	</div>
</div>
<div id="alpha-back" class="none">
	<div id="media-modal"></div>
</div>