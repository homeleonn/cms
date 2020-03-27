@extends('layout')
@section('content')
<?//=dd(get_defined_vars())?>
<div class="tcontent">
	{!! $post->content !!}
</div>
@endsection