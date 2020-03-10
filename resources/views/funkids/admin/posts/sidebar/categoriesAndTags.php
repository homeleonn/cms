<?php 
use Jump\helpers\Common;
//var_dump(get_defined_vars());exit;
if(Common::isPage()) return;

$selfTerms = isset($data['selfTerms']) ? $data['selfTerms'] : [];

if(isset($options['taxonomy'])):
	foreach($options['taxonomy'] as $taxonomy => $taxValues):
?>

<!-- Block for add post <?=$taxonomy?> -->
<div id="post-<?=$taxonomy?>" class="side-block">
	<div class="block-title"><?=$taxValues['title']?></div>
	<div class="block-content">
		<div id="term-<?=$taxonomy?>">
			<?php 
				if(isset($data['terms'])):
					showTermHierarchy($data['terms'], $taxonomy, $selfTerms);
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

function showTermHierarchy(&$terms, $taxonomy, $selfTerms, $level = 0){
	foreach($terms as $key => $termData):
		if($termData['taxonomy'] != $taxonomy) continue;
		$checked = in_array($termData['id'], $selfTerms) ? 'checked' : '';
		echo str_repeat('&nbsp;', $level * 5);
		?>
			<label><input type="checkbox" name="terms[<?=$taxonomy?>][]" value="<?=$termData['id']?>" <?=$checked?> /> <?=$termData['name']?></label><br>
		<?php 
		if(isset($termData['children']))
			showTermHierarchy($termData['children'], $taxonomy, $selfTerms, $level + 1);
		unset($terms[$key]);
	endforeach;
}
?>