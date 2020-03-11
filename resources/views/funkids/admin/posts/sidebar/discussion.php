<?php
$commentsChecked = '';
if(!isset($post['comment_status'])){
	if(!$postOptions['hierarchical'])
		$commentsChecked = '';
}else{
	if($post['comment_status'] == 'open')
		$commentsChecked = 'checked';
}
?>

<div id="post-discussion" class="side-block">
	<div class="block-title">Обсуждение</div>
	<div class="block-content">
		<label><input type="checkbox" name="discussion" <?=$commentsChecked?>> Комментирование</label>
	</div>
</div>