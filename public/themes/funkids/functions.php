<?php

function funKids_all(){
	$controller = Options::get('controller');
	$programs = (new $controller)->run('program', 'actionList', [1, null, null, 30])->getData()['post'];
	// ddd($programs);
	?>
	<div class="all-progs" id="all-progs">
		<h2 class="section-title">Шоу программы аниматоров</h2>
		<div class="twrapper">
			<div class="row flex">
			<?php foreach($programs['__list'] as $item): ?>
				<div class="item col-md-3 col-sm-6 col-xs-12 center">
				<a href="<?=$item->permalink?>">
					<div class="img-wrapper">
						<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item->short_title ?: $item->title?>" />
					</div>
					<div class="inline-title"><?=$item->short_title ?: $item->title?></div>
				</a>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
	/*@foreach($programs['__list'] as $item)
				<div class="item col-md-3 col-sm-6 col-xs-12 center">
				<a href="{{ $item->permalink }}">
					<div class="img-wrapper">
						<img src="{{ postImgSrc($item, 'medium') }}" data-src="{{ postImgSrc($item) }}" class="lazy" alt="{{ $item->short_title ?: $item->title }}" />
					</div>
					<div class="inline-title">{{ $item->short_title ?: $item->title }}</div>
				</a>
				</div>
			@endforeach*/
}

function funKids_services(){
	$controller = Options::get('controller');
	$services = ((new $controller)->run('service', 'actionList', [1, null, null, 30]))->getData()['post'];
	?>
	<div class="extra-services front-page" id="extra-services">
		<h2 class="section-title"><div>Дополнительные</div> <div>услуги</div></h2>
		<div class="container">
			<h3 class="center">Хотите чего-то необычного?<br> Закажите дополнительные атрибуты к детскому празднику, которые оставят незабываемые впечатления!</h3>
			<div class="flex">
			<?php foreach($services['__list'] as $item): ?>
				<div class="item">
					<a href="<?=$item->permalink?>">
						<div class="img2">
							<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item->short_title ?: $item->title?>" />
						</div>
						<div class="inline-title center"><?=$item->short_title ?: $item->title?></div>
					</a>
				</div>
			<?php endforeach; ?>
			</div>
			<?php  /*<!--<div class="center"><a href="<?=uri('services')?>" class="button">Все доп. услуги</a></div>-->*/?>
		</div>
	</div>
	<?php
}

addAction('wrapper_classes', 'funkids_wrapper_classes');
function funkids_wrapper_classes($post){
	$classes = '';
	if (isMain()) $classes .= ' main';
	if (isset($post['post_type']) && $post['post_type'] == 'program') $classes .= ' program-post';
	
	echo $classes;
}