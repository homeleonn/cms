<?//=dd(get_defined_vars());?>
@extends('layout')

@section('content')
<div class="container">
	<h1>{{ $post['short_title'] ?? $post['h1'] ?? $post['title'] }}</h1>
	<div class="floatimg main-img">
		<a href="<?=postImgSrc($post)?>" class="shower">
			<img src="<?=postImgSrc($post, 'medium')?>" alt="<?=$h1??''?>">
		</a>
	</div>
	<?php //if(isset($terms)) echo $terms;?>
	<?//=applyFilter('single_before_content', $post)?>
	<div  class="tcontent"><?=$post['content']?></div>
	<?php //include $this->get('comments');?>
	<?php //if($post_type == 'service') funkids_ilike($id, $post_type, getPageOptionsByType($post_type)['title']);?>
</div>
@endsection