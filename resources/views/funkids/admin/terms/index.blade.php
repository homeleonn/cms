@extends('layout')
@section('content')
<a href="{{ route($postOptions['type'] . '.term_create') }}?term={{$term}}" class="action-tool plus" title="Добавить">
	<span class="icon-plus">Добавить</span>
</a>
<div style="overflow-x: auto;">
	<?=$terms?>
</div>
@endsection