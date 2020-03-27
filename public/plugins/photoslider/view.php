<?php
// dd(basename(__DIR__), __DIR__);
?>



<?php
if ($id == 100) {
	$pathToPluginFiles = '/plugins/' . basename(__DIR__);
	?>
	<link rel="stylesheet" href="<?=$pathToPluginFiles?>/style.css">
	<div class="program-video"><div class="clearfix"></div>
		<div class="section-title center">Примеры выступлений</div><br /><br />
		<div class="newslider " data-src="http://funkids.od.ua/content/uploads/2018/09/M28wjj62UMd.jpg, http://funkids.od.ua/content/uploads/2018/09/wNNTI312Te6d.jpg"></div>
		<div class="center" style="margin: 100px 0;">
			<iframe width="560" height="315" src="https://www.youtube.com/embed/Hj-ltk-ltDU" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
		</div>
	</div>
	<script src="<?=$pathToPluginFiles?>/js.js"></script>
	<?php
}

?>

