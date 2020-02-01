@extends('admin.layouts.layout')

@section('content')
	<h1>Category adding</h1>
	
	@include('admin.layouts.errors')
	
	{{ Form::open(['route' => 'categories.store']) }}
		<input type="text" name="name" placeholder="name"><br>
		<button>Add</button>
	{{ Form::close() }}
@endsection