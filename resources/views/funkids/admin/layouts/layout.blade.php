<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>{{$title ?? ''}} | Панель администратора</title>
	<link rel="stylesheet" type="text/css" href="/admin/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="/admin/css/fontello.css">
	<link rel="stylesheet" type="text/css" href="/admin/css/style.css">
	<link rel="stylesheet" type="text/css" href="/admin/css/style1.css">
	<!--<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,800,700,600"/>-->
	<link rel="shortcut icon" type="image/x-icon" href="/admin/img/favicon.ico"/>
	<script src="/admin/js/jq3.js"></script>
	<script src="/admin/js/admin.js"></script>
</head>
<body>
<div id="wrapper">
	<header>
		<div id="tools">
			<ul>
				<li><a href="#" id="clear-cache">Очистить кеш</a></li>
				<li><a href="{{route('index')}}" target="_blank" class="icon-home" title="Открыть сайт"></a></li>
				<li><a href="{{route('index')}}user/exit/" class="icon-logout" title="Выйти"></a></li>
			</ul>
		</div>
		<div>
			<div id="icon-menu" class="icon-menu" title="Свернуть/Развернуть меню"></div>
			<div id="logo"><img src="/admin/img/jump.png"></div>
		</div>
	</header>
	<?php //include 'dashboard.php'; ?>
	<div id="content">
		<?php //doAction('admin_head'); ?>
		@yield('content')
	</div>
	<footer>
	</footer>
</div>
<?php //ju_footer();?>
<script src="/admin/js/common.js"></script>
<script src="/admin/js/upload.js"></script>
<script src="/admin/js/add.js"></script>
<script src="/admin/js/translit.js"></script>
<script src="/admin/js/comments.js"></script>
<script src="/components/js/tinymce/jquery.tinymce.min.js"></script>
<script src="/components/js/tinymce/tinymce.min.js"></script>
<script>

var root = '/', 
ajaxUrl = root + "admin/ajax/",
postType = '{{$options["type"] ?? "false"}}',
contents = ['content', 'description'],
text, editor, tinymceInit = false,
urlPattern = /^$/;

contents.forEach(function(item){
	var item = 'textarea#' + item;
	if($(item).length){
		text = $(item).val();
		$(item).val('');
		return false;
	}
});



</script>


	
<?php //doAction('admin_footer');?>
</body>
</html>