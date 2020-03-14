@extends('layout')
@section('content')
<?php
//dd(get_defined_vars());
if(isset($data['empty'])): echo $data['empty']; else:
?>
<table class="mytable">
	<tr>
		<td width="20%">Плагин</td>
		<td>Описание</td>
	</tr>
	<?php foreach($data['plugins'] as $plugin):?>
	<tr class="option-cell">
		<td <?=$plugin['active']?' style="background-color: lightgreen;"':''?>>
			<?=$plugin['Plugin Name']?>
			<div class="options">
				[<span style="color: coral;" class="pointer edit"><a href="toggle/<?=$plugin['path']?>/"><?=$plugin['active']?'Деа':'А'?>ктивировать</a></span>] 
				<?php if (file_exists(dirname($plugin['src']) . '/settings.php')) echo '[<a href="settings/', $plugin['path'], '">Настройки</a>] ';?>
				[<span style="color: red;" class="pointer remove">Удалить</span>]
			</div>
		</td>
		<td style="text-align: left;">
			<?=$plugin['Description']?>
			<div style="margin-top: 5px;">
				Версия <?=$plugin['Version']?> | 
				Автор: <a href="<?=$plugin['Author URI']?>"></a><?=$plugin['Author']?> | 
				<a href="<?=$plugin['Plugin URI']?>">Сайт плагина</a>
			</div>
		</td>
	<?php endforeach;?>
	</tr>
</table>
<?php
endif;
?>
@endsection