<?php 
use Jump\helpers\Common;
//dd($GLOBALS['jump_actions']['before_post_add']);
//dd($data, get_defined_vars());
?>
<a href="<?=SITE_URL?>admin/<?=$options['type']?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить</span></a>

 
<?php if (!$options['hierarchical']) : $order = getPostOrderType($options['type'])['order'] ?? false;?>
<form method="POST" class="whisper inline">
	<select name="order" id="order">
		<option value="DESC" <?=$order == 'DESC' || !$order ?'selected':''?>>Новые</option>
		<option value="ASC"  <?=$order == 'ASC'?'selected':''?>>Старые</option>
		<option value="DISTINCT" <?=$order == 'DISTINCT'?'selected':''?>>Произвольный</option>
	</select>
</form>
<?php endif;?>

<div style="overflow-x: auto;">
	<!--<table class="mytable">
		<tr align="center">
			<td>title/url</td><td>Дата публикации</td>
		</tr>
		<?php
		
		// foreach($data as $page):
			// $link = '<a target="_blank" href="' . ROOT_URI . (Common::isPage() ? '' : $options['rewrite']['slug'] . '/') . $page['url'] . '/">' . $page['title'] . '</a>';
		/* 
		<tr>
			<td><?=$link;?></td>
			<td><?=$page['created'];?></td>
			<td>
				<a title="<?=$options['edit']?>"  target="blank" href="<?=SITE_URL?>admin/<?=$options['rewrite']['slug']?>/edit/<?=$page['id'];?>/">
					<span class="icon-pencil block"></span>
				</a>
			</td>
			<td>
				<a href="javascript:void(0);" title="<?=$options['delete']?>" onclick="if(confirm('Подтвердите удаление')) delItem(this,'<?=$options['rewrite']['slug']?>',<?=$page['id'];?> );">
					<span class="icon-cancel red block"></span>
				</a>
			</td>
		</tr> */
		//endforeach; 
		?>
	</table>-->
	<?=$data;?>
</div>