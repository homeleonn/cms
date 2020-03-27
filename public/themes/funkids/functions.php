<?php

function funKids_programs(){
	$programs = funkids_getPostsByType('program');
	?>
	<div class="all-progs" id="all-progs">
		<h2 class="section-title">Шоу программы аниматоров</h2>
		<div class="twrapper">
			<div class="row flex">
			<?php foreach($programs as $item): ?>
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
}

function funKids_services(){
	$services = funkids_getPostsByType('service');
	?>
	<div class="extra-services front-page" id="extra-services">
		<h2 class="section-title"><div>Дополнительные</div> <div>услуги</div></h2>
		<div class="container">
			<h3 class="center">Хотите чего-то необычного?<br> Закажите дополнительные атрибуты к детскому празднику, которые оставят незабываемые впечатления!</h3>
			<div class="flex">
			<?php foreach ($services as $item): ?>
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

function funkids_reviews(){
	$cacheFileName = 'reviews';
	$reviews = funkids_getPostsByType('review', 10);
	?>
	
	<h3 class="inline-title center mb30">Последние отзывы наших клиентов</h3>
	
	<div class="reviews topoffset" id="reviews">
		<div class="carousel-widget container-fluid" data-carousel-widget-column="3">
			<div class="widget-head">
				<div class="title"></div>
				<div class="controls">
					<div class="rightside"></div>
					<div class="leftside"></div>
				</div>
			</div>
			<div class="widget-content">
				<div class="inside-content">
				<?php foreach($reviews as $review):?>
					<div class="item">
						<div class="floatimg sprite reviewimg"></div>
						<p class="quote-big">
							<?=$review->content?>
						</p>
						<div class="right fs22"><?=$review->meta->name?></div>
					</div>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
	<div class="container center">
		<a href="<?=url('reviews')?>" class="button">Перейти ко всем отзывам</a>
		<a href="#" class="button get-review-form">Оставить отзыв</a>
	</div>
	<?php
	
}

addFilter('single_before_content', 'funkids_single_price');
function funkids_single_price($post){
	if (isset($post['_jmp_program_price']))
		echo '<div class="price">', htmlspecialchars_decode($post['_jmp_program_price']), '</div><br>';
}

function funKids_catalogHeroes(){
	// global $funKidsCacheFileNames, $thatCache;
	// $thatCache = true;
	// if(Common::getCache($funKidsCacheFileNames['catalog'], -1)) return;
	// $heroes = (new PostController('program'))->actionList(NULL, NULL, 1, 30, [['created'], 'ASC']);
	$heroes = funkids_getPostsByType('program', 30);
	// dd($heroes);
	$heroes = array_reverse($heroes);
	$heroesImgs = [];
	foreach($heroes as $h){
		$heroesImg = postImgSrc($h, 'medium');
		?>
		<article class="item"><a href="<?=$h->permalink?>"><?=$h->short_title ?? $h->title?></a>
			<div class="preview center">
				<noscript><img src="<?=$heroesImg?>" alt="<?=$h->title?>" /></noscript>
				<div class="inline-title"><h1><?=$h->short_title ?? $h->title?></h1></div><?=strip_tags(mb_substr($h->content, 0 ,200)).'...'?>
			</div>
		</article>
		<?php
		
		$heroesImgs[] = $heroesImg;
	}
	?>
	<script>
		$$(function(){
			var heroesImgs = <?=json_encode($heroesImgs)?>;
			$('.heroes-catalog .list').one('mouseover', function(){
				$('.heroes-catalog > .list > article > .preview').each(function(i){
					$(this).prepend('<img src="'+heroesImgs[i]+'">');
				});
			});
		});
	</script>
	<?php
	// echo Common::setCache($funKidsCacheFileNames['catalog']);
}

function funkids_readyToHolyday(){
?>
<div class="holyday" id="holyday">
	<h2 class="section-title">Готовимся к празднику уже сейчас</h2>
	<div id="order-question">
		Напишите нам и наш менеджер ответит на все Ваши вопросы
		<div class="inp1">
			<input type="text" id="qname" name="name" placeholder="Имя*">
			<input type="text" id="qtel" name="tel" placeholder="Телефон*">
			<input type="text" id="qmail" name="email" placeholder="Электронная почта">
		</div>
		<div class="captcha-wrapper none center">
			<img alt="captcha" class="captcha pointer captcha-reload">
			<span class="icon-arrows-cw captcha-reload" title="Обновить капчу"></span><br>Введите символы с картинки 
			<input type="text" class="captcha-code">
		</div>
		<input type="button" class="button1" id="q-set" value="Отправить">
	</div>
</div>
<?php
}

function funkids_getBlock($blockName){
	$funkName = 'funkids_' . $blockName;
	if (!function_exists($funkName)) return;
	$cacheFileName = 'blocks/' . $blockName;
	if (_Cache::getCache($cacheFileName, -1, false)) return;
	$funkName();
	return _Cache::setCache($cacheFileName);
}

function funkids_getPostsByType($type, $limit = 30)
{
	$controller = Options::get('controller');
	return ((new $controller)->run($type, 'actionList', [1, null, null, $limit]))->getData()['post']['__list'];
}

addAction('wrapper_classes', 'funkids_wrapper_classes');
function funkids_wrapper_classes($post){
	$classes = '';
	if (isMain()) $classes .= ' main';
	if (isset($post['post_type']) && $post['post_type'] == 'program') $classes .= ' program-post';
	
	echo $classes;
}

addFilter('before_return_posts', 'funkids_before_return_posts');
function funkids_before_return_posts($posts){
	if (isset($posts[0]) && $posts[0]->post_type == 'review') {
		foreach ($posts as &$p) {
			$p = funkids_review_encode_meta($p);
		}
	}
	
	return $posts;
}

addFilter('before_return_post', 'before_return_post');
function before_return_post($post){
	if ($post->post_type == 'review') {
		$post = funkids_review_encode_meta($post);
	}
	
	return $post;
}

function funkids_review_encode_meta($post){
	if (isset($post->meta)) {
		$post->meta = json_decode($post->meta);
	}
	
	return $post;
}


