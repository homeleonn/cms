@extends('layout')
@section('content')
<a href="{{ route($postOptions['type'] . '.term_create') }}?taxonomy={{$taxonomy}}" class="action-tool plus" title="Добавить">
	<span class="icon-plus">Добавить</span>
</a>
<div style="overflow-x: auto;">
	<?=$terms?>
</div>

<script>
	$('form.termdel').submit(function(e) {
		this.prepend(addField('_token', '<?=csrf_token()?>'));
		this.prepend(addField('_method', 'delete'));
		this.prepend(addField('taxonomy', '<?=$_GET['taxonomy'] ?? ''?>'));
	});
</script>
@endsection