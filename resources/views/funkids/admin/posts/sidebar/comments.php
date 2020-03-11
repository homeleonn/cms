<?php 
if(isset($post['comments']['general']) && $post['comments']['general']):
?>

<div id="post-comments" class="side-block">
	<div class="block-title">Комментарии (<span id="comment-count"><?=$post['comments']['count'] ? $post['comments']['count']:0?></span>)</div>
	<div class="block-content clearfix" style="margin: 0 auto;">
		<?php
			foreach($post['comments']['general'] as $comment):
				echo themeHTMLCommentTable($comment, isset($post['comments']['sub'][$comment['comment_id']]) ? $post['comments']['sub'][$comment['comment_id']] : NULL, 0);
			endforeach; ?>
	</div>
</div>
<?php endif; ?>