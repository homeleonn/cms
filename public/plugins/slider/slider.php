<?php

/*
 * Plugin Name: Slider
 * Plugin URI: 
 * Description: Slider
 * Version: 0.1
 * Author: Anonymous
 * Author URI: 
 * License: 
 */

addAction('plugin_settings', 'mySlider');

function mySlider(){
	// $slider = getOption('MySliders', true);
	// $slider['images'] = ['glavnii' => [
	// ['img' => '1.jpg', 'title' => 'Праздник дня рождения', 'text' => 'Веселые ребята и их родители с нашими аниматорами'],
	// ['img' => '2.jpg', 'title' => 'Праздник дня рождения', 'text' => 'Веселые ребята и их родители с нашими аниматорами'],
	// ['img' => '3.jpg', 'title' => 'Праздник дня рождения', 'text' => 'Веселые ребята и их родители с нашими аниматорами'],
	// ]];
	// setOption('MySliders', $slider, true);
	include 'settings.php';
}

addAction('plugin_settings_save', 'mySliderSettingsSave');

function mySliderSettingsSave(){//d($_POST, $_FILES);
	if (isset($_FILES['files'])) {
		$dest = __DIR__ . '/images/glavnii/';
		$i = 0;
		$j = -1;
		$new = [];
		$mySlider = getOption('MySliders', true);
		$maxKey = max(array_keys($mySlider['images']['glavnii']));
		foreach ($_FILES['files']['tmp_name'] as $photo) {
			$ext = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
			do{
				$j++;
				$newFilename = "{$dest}{$j}.{$ext}";
			}while(file_exists($newFilename));
			move_uploaded_file($photo, $newFilename);
			$i++;
			$new[++$maxKey] = [$j, $ext];
			$mySlider['images']['glavnii'][] = ['img' => "{$j}.{$ext}", 'title' => '', 'text' => ''];
		}
		setOption('MySliders', $mySlider, true);
		echo json_encode($new);
		Common::clearCache('pages/' . getOption('front_page'));
	} 
	
	else if(isset($_POST['del'])) {
		$mySliders = getOption('MySliders', true);
		//dd($_POST['del'], $mySliders['images']['glavnii'][(int)$_POST['del']]);
		if (isset($mySliders['images']['glavnii'][(int)$_POST['del']])) {
			unlink(__DIR__ . "/images/glavnii/{$mySliders['images']['glavnii'][$_POST['del']]['img']}");
			unset($mySliders['images']['glavnii'][$_POST['del']]);
			setOption('MySliders', $mySliders, true);
			echo 'OK';
		} else {
			echo 'ERR';
		}
		Common::clearCache('pages/' . getOption('front_page'));
	}
	
	else if(isset($_POST['sort'])) {
		$mySliders = getOption('MySliders', true);
		$slider = $mySliders['images']['glavnii'];
		$new = [];
		foreach ($_POST['sort'] as $key => $slide) {
			$new[] = $slider[explode('sort-', $slide)[1]];
		}
		$mySliders['images']['glavnii'] = $new;
		//dd($mySliders);
		setOption('MySliders', $mySliders, true);
		Common::clearCache('pages/' . getOption('front_page'));
		echo json_encode($new);
	} 
	
	else if (isset($_POST['edit-slide'])) {
		$mySliders = getOption('MySliders', true);
		$slider = $mySliders['images']['glavnii'];
		$slider[$_POST['edit-slide'][0]]['title'] = $_POST['edit-slide'][1];
		$slider[$_POST['edit-slide'][0]]['text'] = $_POST['edit-slide'][2];
		$mySliders['images']['glavnii'] = $slider;
		setOption('MySliders', $mySliders, true);
		Common::clearCache('pages/' . getOption('front_page'));
	}
	
	exit;
}

addAction('header_after_menu', 'mySliderView');

function mySliderView($slider){
	$mySliders = unserialize(Options::get('MySliders'));
	$slider1 = $mySliders['images']['glavnii'];
	?>
	<!--SLIDER(+-)-->
	
	<script>
		$$(function(){
			let sliderImgs = <?=json_encode($slider1)?>;
			setTimeout(function(){
				for (key in sliderImgs) {
					render(sliderImgs[key]['img'], sliderImgs[key]['title'], sliderImgs[key]['text']);
				}
				$('.slider-wrapper').removeClass('none');
			}, 500);
			
			function render(img, title, text, active = false){
				let active1 = '', img1 = '/img/1x1.gif" data-src="<?="/plugins/slider/images/{$slider}/"?>';
				if (active) {
					active1 = ' active1';
					img1 = '<?="/plugins/slider/images/{$slider}/"?>';
				}
				
				$('.slider > .ss').append('<div class="item'+(active1)+'"><img src="'+img1+img+'" />'+(title && text ? ('<div class="slider-title"><div>'+title+'</div><div>'+text+'</div></div>') : '')+'</div>');
			}
		});
	</script>
	<div class="slider-wrapper none">
		<div class="slider">
			<div class="ss">
				<div class="item active">
					<div class="yout"><div class="yplay">&#9658;</div><img data-youtube="ZfZfm1twto8" src="<?=theme_url()?>img/mini-gallery/50.jpg" alt="Аниматор трансформер Оптимус Прайм на детский праздник Одесса"></div>
					</div>
			</div>
			<div class="controls">
				<div class="arr-left"></div>
				<div class="arr-right"></div>
			</div>
			<div class="progressbar"></div>
		</div>
	</div>
	<?php
}