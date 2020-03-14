@extends('layout')
@section('content')
	<h1>{{ $postOptions['title'] }}</h1>
	<a href="{{ route($postOptions['type'] . '.create') }}" class="action-tool plus" title="Добавить">
		<span class="icon-plus">Добавить</span>
	</a>
	<?php if (!$postOptions['hierarchical']):
		$order = (\Options::get('post_order_' . $postOptions['type'], true)['order']) ?? false;
	?>
	<form method="POST" class="whisper inline">
		<select name="order" id="order">
			<option value="DESC" <?=$order == 'DESC' || !$order ?'selected':''?>>Новые</option>
			<option value="ASC"  <?=$order == 'ASC'?'selected':''?>>Старые</option>
			<option value="DISTINCT" <?=$order == 'DISTINCT'?'selected':''?>>Произвольный</option>
		</select>
	</form>
	<?php endif;?>

	<div style="overflow-x: auto;">
		{!! $posts !!}
	</div>
@endsection