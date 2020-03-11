<?php if (isset($post['listForParents1'])): ?>
<!-- Block for post properties -->
<div id="post-properties" class="side-block">
	<div class="block-title">Свойства <?=(isset($_GET['term']) ? 'термина' : 'страницы')?></div>
	<div class="block-content">
		<div><b>Родительская</b></div>
		<div style="margin-bottom: 20px;"><?=$post['listForParents1']?></div>
		<?php if(isset($post['templates']) && $post['templates']):?>
		<div><b>Шаблон</b></div>
		<div style="margin-bottom: 20px;"><?=$post['templates']?></div>
		<?php endif;?>
	</div>
</div>
<?php endif; ?>