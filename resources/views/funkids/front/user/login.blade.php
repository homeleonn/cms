@extends('layout')

@section('content')
	<form action="{{ route('user.auth') }}" method="POST" id="loginform">
		{{ csrf_field() }}
		Почта<br>
		<input type="text" name="email" id="email"><br>
		Пароль<br>
		<input type="password" name="password" id="pass"><br>
		<input type="submit" value="Вход">
	</form>
@endsection