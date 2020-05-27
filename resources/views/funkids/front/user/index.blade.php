@extends('layout')

@section('content')
<?php
//	dd(session()->all());
?>
<div class="container">
	User room, <?=session('user.name')?><br>
	@if (isAdmin())
		<a href="{{ url('/admin/') }}">Администраторская</a>
	@endif
	<br>
	<form action="{{ route('user.logout') }}" method="POST">
		{{ csrf_field() }}
		<button>Выход</button>
	</form>
</div>
@endsection