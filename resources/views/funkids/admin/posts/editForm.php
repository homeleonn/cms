<?php  
//dd(get_defined_vars());
?>
<?=doAction('admin_post_options_form');?>
<a href="<?=SITE_URL?>admin/<?=$options['type']?>/add/" class="action-tool plus" title="Добавить"><span class="icon-plus">Добавить новую</span></a>
<h2><?=$options['edit']?></h2>
<form method="POST" id="edit-<?=$options['type']?>" class="post-from-admin" name="" autocomplete="off" enctype="multipart/form-data">
	<div id="center" class="col-md-8">
		<input type="hidden" name="id" id="post_id" value="<?=$data['id']?>">
		<input type="hidden" name="url" value="<?=$data['url']?>">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" value="<?=$data['title']?>" type="text" name="title" id="title" placeholder=""></div>
			<div>
				<a id="url" href="<?=$data['permalink']?>"><span class="anchor"><?=$data['anchor']?></span><span class="editing-part"><?=$data['url']?></span><span id="url-end">/</span></a> 
				<input type="button" id="edit-url-init" value="Изменить" style="padding: 0px 4px;">
				<input type="button" id="edit-url-ok" value="ок" style="padding: 0px 4px; display: none;">
				<input type="button" id="edit-url-cancel" value="отмена" style="padding: 0px 4px; display: none;">
			</div>
		</div>
		
		<div class="block1">
			<div>Краткий заголовок <span class="icon-help-circled" title="хлебные крошки, меню. Если главный заголовок имеет небольшую длинну, данное поле можно не заполнять"></span></div>
			<div><input class="w100" value="<?=$data['short_title']?>" type="text" name="short_title" id="short_title" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Текст</div>
			<div id="editors"><textarea class="visual" name="content" id="content" value="1" style="width: 100%;height: 600px;display: none; visibility:hidden;"><?=htmlspecialchars($data['content'])?></textarea></div>
		</div>
		<?php doAction('edit_post_after');?>
		<?php //include $this->getFile('sidebar/comments');?>
		<?php include $this->getFile('sidebar/extraFields');?>
	</div>
	<div id="sidebar-right" class="col-md-4">
		<br><br><span class="icon-calendar"></span> Добавлено: <?=$data['created']?>
		<br><span class="icon-pencil"></span> Последнее редактирование: <?=$data['modified']?>
		<br><br><input type="button" id="item-factory" value="Редактировать">
		
		<?php include $this->getFile('sidebar/categoriesAndTags');?>
		<?php include $this->getFile('sidebar/listForParents');?>
		<?php //include $this->getFile('sidebar/discussion');?>
		<?php include $this->getFile('sidebar/image');?>
	</div>
</form>

<?php include $this->getFile('sidebar/extra-field-prototype');?>