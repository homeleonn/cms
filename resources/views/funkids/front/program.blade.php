@extends('layout')
@section('content')
<?//=dd(get_defined_vars())?>


<div class="container program">
	<noindex>
	<div class="col-md-3 col-xs-0 heroes-catalog-wrapper">
		<div class="heroes-catalog">
			<div class="ribbon"><div class="title center ribbon-content">КАТАЛОГ ГЕРОЕВ</div></div>
			<div class="list">
				<?php $heroes = funKids_catalogHeroes()?>
			</div>
		</div>
	</div>
	</noindex>
	<div class="col-md-9 shower prog-content">
		<?=isset($h1)?'<h1>'.$post->h1.'</h1>':''?>
		<div class="floatimg main-img">
			<a href="<?=postImgSrc($post)?>" title="<?=$post->short_title?>" class="shower">
				<img src="<?=postImgSrc($post, 'medium')?>" data-large-img="<?=postImgSrc($post)?>" alt="<?=$post->h1 ?? ''?>">
			</a>
		</div>
		<?php if(isset($terms)) echo $post->terms;?>
		<div class="tcontent">
			<?=applyFilter('single_before_content', $post)?>
			<?=$post->content?>
		</div>
	</div>
	
	<noindex>
	<div class="container">
		<?=funkids_inProgram()?>
	</div>
	</noindex>
</div>

<?php
	doAction('photoslider', $post->id);
?>

<div class="container">
<noindex>
	<div class="container">
		<div class="sep"></div>
		<div class="inline-title small center">Не забывайте о наших <a class="under" href="<?=url('services')?>">дополнительных услугах</a>, что бы сделать праздник еще ярче! <br><a href="<?=url('services')?>" class="button">Перейти к доп. услугам</a></div>
		
	</div>
	
	<div class="sep"></div>
	
	<?php //include $this->get('comments');?>
	<?php funKids_like($post->id, $heroes)?>
	</noindex>
</div>
@endsection