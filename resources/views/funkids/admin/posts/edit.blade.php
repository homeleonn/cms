@extends('admin.layouts.layout')

@section('content')
	<h1>post edit</h1>
	
	@include('admin.layouts.errors')
	
	{{ Form::open(['route' => ['post.update', $post->id], 'method' => 'put']) }}
		<input type="text" name="title" placeholder="name" value="{{ $post->title }}"><br>
		<input type="text" name="slug" placeholder="slug" value="{{ $post->slug }}"><br>
		<input type="text" name="short_title" placeholder="short_title" value="{{ $post->short_title }}"><br>
		<textarea name="content" id="" cols="30" rows="10" placeholder="content">{{ $post->content }}</textarea>
		<button>Edit</button>
	{{ Form::close() }}
@endsection