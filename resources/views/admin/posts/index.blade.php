@extends('admin.layouts.layout')
@section('content')
	<h1>Categories</h1>
	<a href="{{ route('posts.create') }}"><button>Add</button></a>
	<table border="1">
		<tr>
			<td>ID</td>
			<td>Title</td>
			<td>Post type</td>
			<td>Author</td>
			<td>Created_at</td>
			<td>Updated_at</td>
		</tr>
		@foreach ($posts as $post)
		<tr>
			<td><a href="{{ route('posts.edit', $post->id) }}">{{ $post->id }}</a></td>
			<td>{{ $post->title }}</td>
			<td>{{ $post->post_type }}</td>
			<td>{{ $post->author }}</td>
			<td>{{ $post->created_at }}</td>
			<td>{{ $post->updated_at }}</td>
			<td>
				{{ Form::open(['route' => ['posts.destroy', 'id' => $post->id], 'method' => 'delete']) }}
					<button onclick="return confirm('You are sure?')">x</button>
				{{ Form::close() }}
			</td>
		</tr>
		@endforeach
	</table>
@endsection