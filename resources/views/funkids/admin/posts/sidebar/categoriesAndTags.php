<?php 
// dd(get_defined_vars());
if($postOptions['type'] == 'page') return;

$selfTerms = isset($post['selfTerms']) ? $post['selfTerms'] : [];

if(isset($postOptions['taxonomy'])):
	foreach($postOptions['taxonomy'] as $taxonomy => $taxValues):
?>

<!-- Block for add post <?=$taxonomy?> -->
<div id="post-<?=$taxonomy?>" class="side-block">
	<div class="block-title"><?=$taxValues['title']?></div>
	<div class="block-content">
		<div id="term-<?=$taxonomy?>">
			<?php 
				if(isset($post['terms'])):
					$post->terms = $post->__model->showTermHierarchy($post->terms, $taxonomy, $selfTerms);
				endif;
			?>
		</div>
		<div><input type="text" id="new-<?=$taxonomy?>"></div>
		<div><input type="button" value="<?=$taxValues['add']?>" onclick="addTerm('<?=$taxonomy?>')"></div>
	</div>
</div>

<?php
	endforeach;
endif;
?>