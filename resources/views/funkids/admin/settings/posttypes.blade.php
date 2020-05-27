@extends('layout')

@section('content')
<?php

// d(get_defined_vars());
?>

<style>
.posttypes{
	padding-left: 50px;
}

.posttypes > div{
	padding: 5px;
	margin: 5px 0;
}

.posttypes > div > input[type="text"]{
	width: 70%;
}


.posttypes > div > span{
	display: inline-block; 
	width: 200px;
}
</style>
<script>

	(() => {
		let posttypes = <?=json_encode($posttypes)?>;
		let i = 0;
		let item = {name:'',type:'',archive:'',hierarchical:''};
		
		$(() => {
			$('.add-posttype').click(() => {
				addPost(item)
			});
			
			
			
			if (typeof posttypes === "object") {
				for (q in posttypes) {
					addPost(posttypes[q]);
				}
			} else {
				posttypes.forEach(addPost);
			}
			
			$('.delete-posttype').click(function() {
				$(this).closest('.posttypes').remove();
			});
			
		});
		
		function addPost(item) {
			$('.posttypes-wrapper').prepend(`
				<div class="posttypes">
					<h2 style="display: inline;">${item.name}</h2>
					<span class="red delete-posttype" style="font-size: 25px; cursor: pointer;"><b>x</b></span>
					<div><span>Имя:</span><input type="text" name="posttypes[${i}][name]" value="${item.name}"></div>
					<div><span>Тип:</span><input type="text" name="posttypes[${i}][type]" value="${item.type}"></div>
					<div><span>Архив:</span><input type="text" name="posttypes[${i}][archive]" value="${item.archive ? item.archive : ''}"></div>
					<div><span>Иерархический:</span><input type="checkbox" name="posttypes[${i}][hierarchical]" ${item.hierarchical ? 'checked' : ''}></div>
					<hr>
					<hr>
				</div>
			`);
			
			i++;
		}
	})();
</script>
<form action="{{route('admin.posttypes.save')}}" method="POST">
	{{csrf_field()}}
	<input type="hidden" name="_method" value="PUT">
	<?php /*@forelse ($posttypes as $key => $posttype)
		<h2>{{ $posttype['name'] }}</h2>
		<div class="posttypes">
			<div><span>Имя:</span><input type="text" name="posttypes[{{$key}}][name]" value="{{ $posttype['name'] }}"></div>
			<div><span>Тип:</span><input type="text" name="posttypes[{{$key}}][type]" value="{{ $posttype['type'] }}"></div>
			<div><span>Архив:</span><input type="text" name="posttypes[{{$key}}][archive]" value="{{ $posttype['archive'] ?? '' }}"></div>
			<div><span>Иерархический:</span><input type="checkbox" name="posttypes[{{$key}}][hierarchical]" {{ isset($posttype['hierarchical']) ? 'checked' : '' }}></div>
		</div>
		<hr>
		<hr>
	@empty
		no data
	@endforelse*/?>
		<div class="icon-plus green add-posttype" style="padding: 10px; margin: 20px;background: lightgreen; display: inline-block;"></div>
		<button>Сохранить</button>
		<div class="posttypes-wrapper">
			
		</div>
</form>
	
@endsection