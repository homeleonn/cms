<?php  
//dd(get_defined_vars());
?>
<?=doAction('admin_post_options_form');?>
<h2><?=$options['add']?></h2>
<form method="POST" id="add-<?=$options['type']?>" class="post-from-admin" name="" autocomplete="off" enctype="multipart/form-data">
	<div id="center" class="col-md-8">
		<input type="hidden" name="id">
		<div class="block1">
			<div>Заголовок</div>
			<div><input class="w100" type="text" name="title" id="title" placeholder=""></div>
		</div>
		<div class="block1">
			<div>Краткий заголовок <span class="icon-help-circled" title="хлебные крошки, меню. Если главный заголовок имеет небольшую длинну, данное поле можно не заполнять"></span></div>
			<div><input class="w100" type="text" name="short_title" id="short_title" placeholder=""></div>
		</div>
		<div class="block1">
			<div>Текст</div>
			<div id="editors"><textarea class="visual" name="content" id="content" value="1" style="width: 100%;height: 600px;display: none; visibility:hidden;"></textarea></div>
		</div>
		<?php doAction('add_post_after', $options);?>
		<?php include $this->getFile('sidebar/comments');?>
		<?php include $this->getFile('sidebar/extraFields');?>
		<?=doAction('add_extra_rows', $options['type']);?>
	</div>
	
	<!-- Block for add post categories -->
	<!-- Block for add post tags -->
	<div id="sidebar-right" class="col-md-4">
		<input type="button" id="item-factory" value="Добавить">

		<?php include $this->getFile('sidebar/categoriesAndTags');?>
		<?php include $this->getFile('sidebar/listForParents');?>
		<?php //include $this->getFile('sidebar/discussion');?>
		<?php include $this->getFile('sidebar/image');?>
	</div>
	
	<div class="sep"></div>
</form>

<?php include $this->getFile('sidebar/extra-field-prototype');?>