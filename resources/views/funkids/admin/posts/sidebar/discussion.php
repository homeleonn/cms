<?php
$commentsChecked = '';
if(!isset($data['comment_status'])){
	if(!$options['hierarchical'])
		$commentsChecked = 'checked';
}else{
	if($data['comment_status'] == 'open')
		$commentsChecked = 'checked';
}
?>

<div id="post-discussion" class="side-block">
	<div class="block-title">Обсуждение</div>
	<div class="block-content">
		<label><input type="checkbox" name="discussion" <?=$commentsChecked?>> Комментирование</label>
	</div>
</div>