@extends('layouts.layout')

@section('content')
	{{ Form::open(['route' => 'categories.store']) }}
		<input type="text" name="name">
	{{ Form::close() }}
@endsection