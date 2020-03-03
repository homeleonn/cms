<?php
$slider = $_GET['slider'] ?? 'glavnii';
$sliders = getOption('MySliders', true);//dd($sliders);
$images = __DIR__ . '/images/';
?>

<link rel="stylesheet" href="<?=PLUGINS?>slider/style.css">
<link rel="stylesheet" href="<?=PLUGINS?>slider/jquery-ui.min.css">
<script src="<?=PLUGINS?>slider/js.js"></script>
<script src="<?=PLUGINS?>slider/jquery-ui.min.js"></script>

<form action="" method="POST" enctype="multipart/form-data">
<div id="plug-my-slider">
	<?php /*<ul id="sliders-list">
		<?php foreach ($sliders['list'] as $key => $slid): ?>
		<li class="item"><a href="?slider=<?=$key?>" <?=($slider && $slider == $key ? 'class="active"' : '')?>><?=$slid?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php*/ if ($slider): ?>
	<!--<hr>
	<h2>Слайдер: <u></u></h2>-->
	<small>Оптимальный размер изображения: 1150х400</small><br><br>
	<input multiple type="file" accept="image/jpeg,image/png" id="sliderphotos" name="sliderphotos" class="none">
	<label for="sliderphotos"><img src="<?=ADMIN_THEME?>img/addfile.png" title="Добавить фотографии"></label>
	<div class="sorted-save">Сохранить</div>
	<div id="sliderphotosContainer" class="row">
		<?php
		//dd(unserialize(getOption('MySliders')));
			//foreach (scandir ($images . $slider) as $img) {
			foreach (getOption('MySliders', true)['images']['glavnii'] as $key => $img) {
				if (!is_dir($img['img'])) {//dd($img);
					$id = (explode('.', $img['img'])[0]);
					?> 
					<div class="col-md-4 slider-img" id="sort-<?=$key?>" data-title="<?=$img['title']?>" data-text="<?=$img['text']?>">
						<img class="shower" src="<?=PLUGINS . 'slider/images/' . $slider . '/' . $img['img']?>">
						<div class="text">&#9998;</div>
						<div class="del">x</div>
					</div> 
					<?php
				}
			} 
		?>
	</div>
	<?php else: echo '<h3>Выберите слайдер</h3>'; endif; ?>
</div>
</form>


<div id="edit-text" class="none">
	Заголовок<br>
	<input type="text" class="slide-title"><br><br>
	Текст<br>
	<input type="text" class="slide-text">
	<br><br>
	<button class="edit-slide-ok">Ок</button>
	<button class="edit-slide-cancel">Отмена</button>
</div>