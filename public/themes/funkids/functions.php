<?php

addPageType('page', [
		'type' => 'page',
		'title' => '',
		'title_for_admin' => 'Страницы',
		'description' => 'Страницы',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
		'hierarchical' => true,
		'has_archive'  => false,
		'taxonomy' => [],
		'rewrite' => ['slug' =>'', 'with_front' => true, 'paged' => false],
]);



addPageType('post', [
		'type' => 'post',
		'title' => 'Блог',
		'h1' => 'Блог',
		'title_for_admin' => 'Статьи',
		'description' => 'Блог',
		'add' => 'Добавить статью',
		'edit' => 'Редактировать статью',
		'delete' => 'Удалить статью',
		'common' => 'статей',
		'hierarchical' => false,
		'has_archive'  => 'blog',
		'taxonomy' => [
			'category' => [
				'title' => 'Категория',
				'add' => 'Добавить категорию',
				'edit' => 'Редактировать категорию',
				'delete' => 'Удалить категорию',
				'hierarchical' => false,
			],
			'tag' => [
				'title' => 'Тег',
				'add' => 'Добавить тег',
				'edit' => 'Редактировать тег',
				'delete' => 'Удалить тег',
				'hierarchical' => true,
			],
		],
		'rewrite' => ['slug' => 'blog/%category%', 'with_front' => false, 'paged' => 20],
]);

addPageType('program', [
		'type' => 'program',
		'title' => 'Программы',
		'_seo_title' => 'Детские аниматоры на день рождения Одесса, утренник, выпускной. Пригласить аниматора для ребенка',
		'h1' => 'Аниматоры, шоу программы на детский праздник в Одессе',
		'title_for_admin' => 'Программы',
		'description' => 'Заказать детского аниматора на день рождения ребенка на дом либо на утренник или выпускной в Одессе, широкий выбор аниматоров и шоу программы, а так же красочные детские ведущие, которые порадуют детей интересными конкурсами и подарят массу ярких впечатлений. Все останутся довольны. Заказать аниматора для ребенка на праздник. Цена',
		'add' => 'Добавить программу',
		'edit' => 'Редактировать программу',
		'delete' => 'Удалить программу',
		'common' => 'программ',
		'hierarchical' => false,
		'has_archive'  => 'programs',
		'rewrite' => ['slug' => 'programs', 'with_front' => false, 'paged' => 30],
		// 'taxonomy' => [
			// 'age' => [
				// 'title' => 'Возрастная категория',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
			// 'gen' => [
				// 'title' => 'Пол ребенка',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
		// ]
]);

addPageType('service', [
		'type' => 'service',
		'title' => 'Доп. услуги',
		'_seo_title' => 'Дополнительные услуги | Funkids',
		'h1' => 'Дополнительные услуги',
		'title_for_admin' => 'Доп. услуги',
		'description' => 'Дополнительные услуги на детский праздник, мыльные пузыри, сладкая вата, всё что бы разнообразить праздничный день, запоминающиеся мгновения жизни ребенка | FunKids',
		'add' => 'Добавить услугу',
		'edit' => 'Редактировать услугу',
		'delete' => 'Удалить услугу',
		'common' => 'услуг',
		'hierarchical' => false,
		'has_archive'  => 'services',
		'rewrite' => ['slug' => 'services', 'with_front' => false, 'paged' => 20],
]);

addPageType('review', [
		'type' => 'review',
		'title' => 'Отзывы',
		'_seo_title' => 'Отзывы | Funkids',
		'h1' => 'Отзывы наших клиентов',
		'title_for_admin' => 'Отзывы',
		'description' => 'Отзывы | FunKids',
		'add' => 'Добавить отзыв',
		'edit' => 'Редактировать отзыв',
		'delete' => 'Удалить отзыв',
		'common' => 'Отзывы',
		'hierarchical' => false,
		'has_archive'  => 'reviews',
		'rewrite' => ['slug' => 'reviews', 'with_front' => false, 'paged' => 20],
]);


// setOption('cache.program', 'funkids/program');
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

// // setOption('cache.like', 'funkids/like');
function funKids_like($id, $programs){
	// global $funKidsCacheFileNames, $thatCache;
	// $thatCache = true;
	// if(Common::getCache($funKidsCacheFileNames['like'].$id, 86400)) return;
	// $popular = (new PostController('program'))->actionList(NULL, NULL, 1, 5, [['id'], ['DESC', 'ASC'][mt_rand(0, 1)]]);
	// shuffle($popular['__list']);
	//dd($popular['__list']);
	
	$programsKeys = array_rand($programs, 5);
	?>
	
	
	<div>
		<div class="carousel-widget container" data-carousel-widget-column="3">
			<div class="widget-head">
				<div class="title">Похожие программы аниматоров</div>
				<div class="controls">
					<div class="rightside"></div>
					<div class="leftside"></div>
				</div>
			</div>		
			<div class="widget-content">
				<div class="inside-content center">
				<?php 
					foreach($programsKeys as $key): 
					$item = $programs[$key];
					if($item->id == $id) continue; 
				?>
					<article class="item">
						<div class="img2">
							<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item->short_title ?: $item->title?>" />
						</div>
						<h1 class="inline-title"><?=$item->title?></h1>
						<?=strip_tags(mb_substr($item->content, 0 ,200)).'...'?>
						<div><a href="<?=$item->permalink?>" class="button">Перейти</a></div>
					</article>
				<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="center"><a href="<?=url('/')?>#all-progs" class="button">Все программы</a></div>
	</div>
	<?php
	// echo Common::setCache($funKidsCacheFileNames['like'].$id);
}

// setOption('cache.service', 'funkids/service');
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


// setOption('cache.funkids/review');
function funkids_reviews() {
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

function funkids_inProgram(){
	?>
	<div class="inline-title in-program">В программу включено</div>
	<div class="row center prog-filling flex line">
		<div><img src="<?=theme_url()?>img/costumes.jpg" alt="Костюмы аниматоров"><div class="inline-title">Красочные костюмы</div></div>
		<div><img src="<?=theme_url()?>img/interactive.jpg" alt="Детская интерактивная программа Одесса"><div class="inline-title">Интерактивная программа</div></div>
		<div><img src="<?=theme_url()?>img/props.jpg" alt="Реквизит на шоу программу, праздник"><div class="inline-title">Реквизит</div></div>
		<div><img src="<?=theme_url()?>img/musical-equipment.jpg" alt="Музыка, музыкальный реквизит для детских аниматоров в Одессе"><div class="inline-title">Музыкальная аппаратура и сопровождение</div></div>
		<div><img src="<?=theme_url()?>img/dj.jpg" alt="Диджей, DJ, Ди-джей, музыка на день рождения ребенка"><div class="inline-title">Диджей</div></div>
	</div>
	<?php
}

addFilter('single_before_content', 'funkids_single_price');
function funkids_single_price($post) {
	if (isset($post['_jmp_program_price'])) {
		echo '<div class="price">', htmlspecialchars_decode($post['_jmp_program_price']), '</div><br>';
	}
}


// setOption('cache.funkids/catalog');
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
	
	return $heroes;
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
	if (getCache($cacheFileName, -1)) return;
	$funkName();
	echo setCache($cacheFileName);
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

/*HEADERIMAGES*/

addAction('add_post_after', 'funkids_headerimage_admin');
addAction('edit_post_after', 'funkids_headerimage_admin');

function funkids_headerimage_admin($post){
	$post['_headerimage_pos'] = $post['_headerimage_pos'] ?? 'center';
	?>
<div class="side-block">
	<div class="block-title">Изображение заголовка страницы</div>
	<div class="block-content">
		Ссылка: 
		<input type="text" class="w100" name="_headerimage" value="<?=$post['_headerimage'] ?? ''?>">
		Позиция:
		<select name="_headerimage_pos" id="_headerimage_pos">
			<option value="top" <?=$post['_headerimage_pos'] == 'top' ? 'selected':''?>>top</option>
			<option value="center" <?=$post['_headerimage_pos'] == 'center' ? 'selected':''?>>center</option>
			<option value="bottom" <?=$post['_headerimage_pos'] == 'bottom' ? 'selected':''?>>bottom</option>
		</select>
	</div>
</div>

<script>
	(function(){
		let start = -20, select = '<?=$post['_headerimage_pos']?>';
		for(var i = 12; i > 0; i--) {
			$('#_headerimage_pos').append('<option value="'+start+'%" '+(select == start+'%' ? 'selected':'')+'>'+start+'%</option>');
			start += 10;
		}
		
	})();
	
</script>
	<?php
}

addAction('jhead', 'funkids_headerimage');

function funkids_headerimage($post){
	if (isset($post['_headerimage'])) :
	?><style>
.program-post .top-sky{display: none;}.program-post .header{background-image: url(<?=$post['_headerimage']?>);background-position-y: <?=$post['_headerimage_pos']??'center'?>;height: 500px;background-size: cover;} .program-post .header-content{height: 100%;background: rgba(8, 0, 129, .2);} @media (max-width: 500px){ .program-post .header{background-position-x: 30%;height: auto;} .program-post .logo-text img {width: 200px;position: relative;top: -50px;} .program-post .logo-text {text-align: left !important;} .program-post nav label > div::before{} .program-post .header .row .logo-text, .program-post .header .row .tels > *:not(div){display: none;} .program-post .header .row .tels{padding-top: 300px;} } 	</style><?php endif;
}

/*-HEADERIMAGES*/

/*PRICE*/
 
function funkids_programPrice($post, $options = null){
	$types = ['program', 'service'];
	// dd($post, $options);
	if (($options && in_array($options['type'], $types)) || (isset($post['post_type']) && in_array($post['post_type'], $types))) {
	
	//dd($post);
	?>
	<div class="side-block">
		<div class="block-title">Стоимость</div>
		<div class="block-content">
			<textarea name="_jmp_program_price" id="_jmp_program_price" rows="3" style="width:100%"><?=$post['_jmp_program_price'] ?? ''?></textarea>
		</div>
	</div>
	<?php
	}
}

addAction('add_post_after', 'funkids_programPrice', true);
addAction('edit_post_after', 'funkids_programPrice', true);
addFilter('extra_fields_keys', 'funkids_extra_fields_keys');

function funkids_extra_fields_keys($extraFieldKeys){
	$extraFieldKeys = array_merge(
		$extraFieldKeys, 
		[
			'_jmp_program_price', 
			'_headerimage', 
			$_POST['_headerimage'] ? '_headerimage_pos' : ''
		]
	);
	
	return $extraFieldKeys;
}


function funClearCache($post){//dd(getPageOptionsByType($post['post_type']));
	global $funKidsCacheFileNames;
	$postOptions = getPostOptionsByType($post['post_type']);
	$frontPage = getOption('front_page');
	
	if($post['post_type'] == 'program'){
		//clearCache($funKidsCacheFileNames['popular']);
		clearCache($funKidsCacheFileNames['catalog']);
		clearCache($funKidsCacheFileNames['programs_all']);
		clearCache('pages/' . $frontPage);
	}
	elseif($post['post_type'] == 'service'){
		clearCache($funKidsCacheFileNames['services']);
		clearCache('pages/' . $frontPage);
	}
	
	if(isset($post['id']) && $post['id'] == $frontPage){
		clearCache('pages/' . $post['id']);
	}
	
	if(isset($post['url'])){
		$preSlug = !$postOptions['rewrite']['with_front'] ? $postOptions['rewrite']['slug'] . '/' : '';
		clearCache('pages/' . md5($preSlug . $post['url']));
	}
	
	if(!$postOptions['hierarchical']){
		clearCache('pages/list-' . $post['post_type']);
	}
}

// addAction('before_post_add', 'funClearCache');
// addAction('before_post_edit', 'funClearCache');
// addAction('before_post_delete', 'funClearCache');
// addAction('reviewDelete', 'funClearCacheReviews');
// addAction('reviewToggle', 'funClearCacheReviews');


