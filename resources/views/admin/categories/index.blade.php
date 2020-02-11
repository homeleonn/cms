@extends('admin.layouts.layout')

@section('content')
	<style>
		table td{
			padding: 3px 5px;
		}
	</style>
	<h1>Categories</h1>
	<a href="{{ route('categories.create') }}"><button>Add</button></a>
	<table border="1">
		<tr>
			<td>ID</td>
			<td>Title</td>
			<td>Created_at</td>
			<td>Updated_at</td>
		</tr>
		@foreach ($categories as $category)
		<tr>
			<td><a href="{{ route('categories.edit', $category->id) }}">{{ $category->id }}</a></td>
			<td>{{ $category->title }}</td>
			<td>{{ $category->created_at }}</td>
			<td>{{ $category->updated_at }}</td>
			<td>
				{{ Form::open(['route' => ['categories.destroy', 'id' => $category->id], 'method' => 'delete']) }}
					<button onclick="return confirm('You are sure?')">x</button>
				{{ Form::close() }}
			</td>
		</tr>
		@endforeach
	</table>
@endsection