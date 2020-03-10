<?php if(!isset($data['listForParents'])) return; ?>
<!-- Block for post properties -->
<div id="post-properties" class="side-block">
	<div class="block-title">Свойства <?=(isset($_GET['term']) ? 'термина' : 'страницы')?></div>
	<div class="block-content">
		<div><b>Родительская</b></div>
		<div style="margin-bottom: 20px;"><?=$data['listForParents']?></div>
		<?php if(isset($data['templates']) && $data['templates']):?>
		<div><b>Шаблон</b></div>
		<div style="margin-bottom: 20px;"><?=$data['templates']?></div>
		<?php endif;?>
	</div>
</div>