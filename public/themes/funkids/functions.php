<?php

function funKids_all(){
	// global $funKidsCacheFileNames, $thatCache;
	// $thatCache = true;
	// $postType = 'program';
	// if(Common::getCache($funKidsCacheFileNames['programs_all'], -1)) return;
	
	// [$order, $ord] = funkids_post_order_default($postType);
	
	// $popular = (new PostController($postType))->actionList(NULL, NULL, 1, 30,  [['id'], $ord] );
	
	// // If distinct order
	// $popular['__list'] = funkids_post_order_distinct($order, $popular['__list']);
	
	ddd(App\Post::where('post_type', 'program')->orderBy('id', 'desc')->take(30)->get());
	
	?>
	<div class="all-progs" id="all-progs">
		<h2 class="section-title">Шоу программы аниматоров</h2>
		<div class="twrapper">
			<div class="row flex">
			<?php foreach($popular['__list'] as $item): ?>
				<div class="item col-md-3 col-sm-6 col-xs-12 center">
				<a href="<?=$item['permalink']?>">
					<div class="img-wrapper">
						<img src="<?=postImgSrc($item, 'medium')?>" data-src="<?=postImgSrc($item)?>" class="lazy" alt="<?=$item['short_title'] ?: $item['title']?>" />
					</div>
					<div class="inline-title"><?=$item['short_title'] ?: $item['title']?></div>
				</a>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
	echo Common::setCache($funKidsCacheFileNames['programs_all']);
}