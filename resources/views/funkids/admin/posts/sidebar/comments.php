<?php /*if(isset($data['comments']) && $data['comments']): ?>
<div id="post-comments" class="side-block">
	<div class="block-title">Комментарии</div>
	<div class="block-content clearfix">
		<?php
			foreach($data['comments'] as $comment):
		?>
		<table>
			<tr>
				<td><?=$comment['comment_author']?></td>
				<td width="100%"><?=$comment['comment_date']?></td>
				<td><span class="icon-cancel" id="comment-delete" data-id="<?=$comment['comment_id']?>"></span></td>
			</tr>
			<tr>
				<td colspan="3"><?=$comment['comment_content']?></td>
			</tr>
		</table>
		<?php endforeach; ?>
		<!--<input type="button" value="Добавить комментарий" id="admin-comment-block-show" class="btn">
		<div id="admin-comments-block" class="none">
			<textarea id="comment-text" cols="30" rows="10" style="width: 100%; height: 150px;"></textarea>
			<input type="button" value="Добавить" id="admin-add-comment" class="btn" style="margin-top: 5px;">
			<input type="button" value="Отмена" id="admin-cancel-comment" class="btn2" style="float: right; margin-top: 5px;">
		</div>-->
	</div>
</div>
<?php endif; ?>*/
//dd($data);
if(isset($data['comments']['general']) && $data['comments']['general']):
?>

<div id="post-comments" class="side-block">
	<div class="block-title">Комментарии (<span id="comment-count"><?=$data['comments']['count'] ? $data['comments']['count']:0?></span>)</div>
	<div class="block-content clearfix" style="margin: 0 auto;">
		<?php
			foreach($data['comments']['general'] as $comment):
				echo themeHTMLCommentTable($comment, isset($data['comments']['sub'][$comment['comment_id']]) ? $data['comments']['sub'][$comment['comment_id']] : NULL, 0);
			endforeach; ?>
	</div>
</div>
<?php endif; ?>