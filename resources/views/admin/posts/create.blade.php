@extends('admin.layouts.layout')

@section('content')
	<h1>Post adding</h1>
	
	@include('admin.layouts.errors')
	
	{{ Form::open(['route' => 'posts.store']) }}
		<input type="text" name="title" placeholder="title"><br>
		<input type="text" name="slug" placeholder="slug"><br>
		<input type="text" name="short_title" placeholder="short_title"><br>
		<textarea name="content" id="" cols="30" rows="10" placeholder="content"></textarea>
		<button>Add</button>
	{{ Form::close() }}
@endsection