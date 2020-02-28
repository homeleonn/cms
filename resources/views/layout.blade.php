<!doctype html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<?php //jmpHead() ?>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&subset=latin,cyrillic" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=theme_path()?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=theme_path()?>css/fontello.css">
	<link rel="stylesheet" href="<?=theme_path()?>css/style.css">
	<link rel="shortcut icon" href="<?=\URL::to('/')?>/favicon.ico" type="image/x-icon">
	<script>let root = "<?=\URL::to('/')?>", theme = "<?=theme_path()?>";function $$(callback){window.addEventListener('load', callback);}</script>
</head>
<body>
	<div class="wrapper<?//=(isset($post) ? doAction('wrapper_classes', $post) : '');?>" id="wrapper">
		<div class="header">
			<?php /*if(isMain()):?>
			<div class="front-menu flex line">
				<a href="#all-progs">Шоу программы</a>¤
				<a href="#extra-services">Доп. услуги</a>¤
				<a href="#s-about">О нас</a>¤
				<a href="#holyday">Обратная связь</a>¤
				<a href="#reviews">Отзывы</a>¤
				<a href="#wrapper">Вверх</a>
				<div class="bottom-phone"><span class="icon-phone" title="Заказать обратный звонок"></span> +38(067) 797-93-85</div>
			</div>
			<?php endif;*/?>
			<div class="top-line"></div>
			<div class="top-sky">
				<div class="clouds sprite"></div>
				<div class="air-balloons-left sprite"></div>
				<div class="air-balloons-right sprite"></div>
			</div>
			<div class="header-content">
				<div class="container">
					<div class="social">
						<a href="https://www.facebook.com/profile.php?id=100004157191483&refsrc=https%3A%2F%2Fm.facebook.com%2Ffbrdr%2F2048%2F100004157191483%2F" rel="nofollow" target="_blank"><div class="facebook icon-facebook-1"></div></a>
						<a href="https://www.youtube.com/channel/UChNlyGg0f5F-mDHxy8UP8IQ" rel="nofollow" target="_blank"><div class="youtube icon-youtube"></div></a>
						<a href="https://www.instagram.com/funkids_odessa/" rel="nofollow" target="_blank"><div class="instagram icon-instagram"></div></a>
						<a href="https://vk.com/club163464318" rel="nofollow" target="_blank"><div class="vk icon-vk"></div></a>
					</div>
					<div class="row">
						<div class="logo-text">
							<a href="<?=\URL::to('/')?>"><img alt="Логотип. Организация детских праздников Одесса. Аниматоры. Шоу программы. Низкие цены" src="<?=theme_path()?>img/logo_trnsprnt1.png"></a>
						</div>
						<div class="tels fs25">
							<a href="tel:+380677979385">(067) 797-93-85</a>
							<a href="tel:+380632008595">(063) 200-85-95</a><br>
							<div class="phone-top"><span class="icon-phone"></span><span>Заказать обратный звонок</span></div>
						</div>
					</div>
					<?php //getMenu(); ?>
					<?php //if(isMain()) doAction('header_after_menu', 'glavnii');?>
				</div>
			</div>
		</div>
		
		<div class="content">
			<div class="container-fluid breadcrumbs">
				<div class="container"><?//=getBreadCrumbs();?></div>
			</div>
				@yield('content')
				</div>
	<footer class="<?=$_SERVER['REQUEST_URI']=='/'?'index':''?>">
			<div class="plain sprite"></div>
			<div class="container-fluid">
				<div class="container">
					<div class="row top">
						<div class="col-md-6">
							<div class="logo-text">
								<a href="<?=\URL::to('/')?>"><img src="<?=theme_path()?>img/logo_trnsprnt1.png" alt="Логотип в футере сайта"></a>
								<div class="inline-title center">Организация детских праздников</div>
							</div>
						</div>
						<div class="col-md-6 center">
							<div class="fs25 mb30 white">
								Позвоните нам:<br>
								<a href="tel:+380677979385">+38 (067) 797-93-85</a><br>
								<a href="tel:+380632008595">+38 (063) 200-85-95</a>
								<br>Почта: <a href="mailto:funkids@mail" class="fs18">funkidssodessa@gmail.com</a>
							</div>
						</div>
					</div>
					
					<div class="copyright">Copyrights © 2012 - 2019: FunKids Odessa</div>
					<div class="social-bottom">	
						<a href="https://vk.com/club163464318" rel="nofollow" target="_blank"><span class="vk icon-vk"></span></a>
						<a href="https://www.instagram.com/funkids_odessa/" rel="nofollow" target="_blank"><span class="instagram icon-instagram"></span></a>
						<a href="https://www.youtube.com/channel/UChNlyGg0f5F-mDHxy8UP8IQ" rel="nofollow" target="_blank"><span class="youtube icon-youtube"></span></a>
						<a href="https://www.facebook.com/profile.php?id=100004157191483&refsrc=https%3A%2F%2Fm.facebook.com%2Ffbrdr%2F2048%2F100004157191483%2F" rel="nofollow" target="_blank"><span class="facebook icon-facebook-1"></span></a>
					</div>
				</div>
			</div>
		</footer>
		<div class="phone" title="Заказать обратный звонок"><span class="icon-phone"></span></div>
		<div class="up" title="Подняться вверх"></div>
	</div>
	<script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js'></script>
	<script src="<?=theme_path()?>js/js.js"></script>
</body>
</html>