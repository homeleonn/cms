@extends('admin.layouts.layout')

@section('content')
	<h1>Category edit</h1>
	
	@include('admin.layouts.errors')
	
	{{ Form::open(['route' => ['categories.update', $category->id], 'method' => 'put']) }}
		<input type="text" name="name" placeholder="name" value="{{ $category->name }}"><br>
		<button>Edit</button>
	{{ Form::close() }}
@endsection