<?php  
//var_dump(get_defined_vars());exit;
//var_dump($options, $data);exit;
if(isset($_GET['msg']))
	echo "<h3 style='padding: 10px;background: lightgreen;'>{$_GET['msg']}</h3>";
?>
<h2><?=$options['taxonomy'][$data['term']]['add']?></h2>
<form method="POST" id="add-term" class="post-from-admin" name="" autocomplete="off">
	<div id="center" class="col-md-8">
		<input type="hidden" name="term" value="<?=$data['term']?>">
		<div class="block1">
			<div>Имя</div>
			<div><input class="w100" type="text" name="name" id="name" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Slug</div>
			<div><input class="w100" type="text" name="slug" id="slug" placeholder=""></div>
		</div>
		
		<div class="block1">
			<div>Описание</div>
			<div><textarea class="nonEditor" name="description" id="description" value="1" style="width: 100%;height: 200px;"></textarea></div>
		</div>
	</div>
	<div id="sidebar-right" class="col-md-4">
		<!--<input type="button" id="item-factory" value="Добавить">-->
		<input type="submit" id="" value="Добавить">
		<?php include $this->getFile('sidebar/listForParents');?>
	</div>
	
	<div class="sep"></div>
	
</form>