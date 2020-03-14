<?php  
//var_dump(get_defined_vars());exit;
if(isset($_GET['msg']))
	echo "<h3 style='padding: 10px;background: lightgreen;'>{$_GET['msg']}</h3>";
$term = $data['term'];
?>
<h2><?=$options['taxonomy'][$term['taxonomy']]['edit']?></h2>
<form method="POST" id="edit-term-<?=$options['type']?>" class="post-from-admin" name="" autocomplete="off">
	<div id="center" class="col-md-8">
		<input type="hidden" name="id" value="<?=$term['id']?>">
		<div class="block1">
			<div>Имя</div>
			<div><input class="w100" type="text" name="name" id="name" value="<?=$term['name']?>" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Slug</div>
			<div><input class="w100" type="text" name="slug" id="slug" value="<?=$term['slug']?>" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Описание</div>
			<div><textarea class="nonEditor" name="description" id="description" value="" style="width: 100%;height: 600px;"><?=$term['description']?></textarea></div>
		</div>
	</div>
	<div id="sidebar-right" class="col-md-4">
		<br><br><input type="submit" id="item-factory" value="Редактировать">
		<?php include $this->getFile('sidebar/listForParents');?>
	</div>
	
	<div class="sep"></div>
	
</FORM>