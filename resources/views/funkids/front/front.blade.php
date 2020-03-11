<?//=var_dump(get_defined_vars());exit;?>
@extends('layout')

@section('content')
<div class=" container" style="margin-top: 400px;">
	<div class="col-sm-12">
		@forelse ($post['__list'] as $item)
			<div class="col-sm-12 front-list" style="margin-bottom: 50px; background: #c8c8c8;">
				<div>
					<a href="{{ $item->slug }}">
						<div class="name" style="font-size: 30px; margin: 5px 5px 10px; color: black;font-weight: bold;">
							{{ $item->title }}
						</div>
					</a>
					<div class="thumb" style="text-align: center;">
						<img style="max-height: 400px; display: inline;" class="shower" src="{{ postImgSrc($item, 'medium') }}">
					</div>
					<div style="background: #9b9b9b;padding: 5px;margin: 10px;border-radius: 20px; font-weight: bold;" class="post-meta">
						<span class="icon-calendar"></span> <a href="{{ $item->slug }}">{{ substr($item->created_at, 0, -3) }}</a>
						
						@if ($item->terms)
						 | <span class="icon-folder"></span> <ul>{{ $item->terms }}</ul>
						@endif
						
						{{--| <span class="icon-comment"></span><a href="{{ $item->slug }}#post-comments">
						{{ $item->comment_count ? "Комментарии({$item->comment_count})" : 'Добавить комменатрий' }}
						</a>--}}
					</div>
				</div>
			</div>
		@empty
			<p>Архивов нет!</p>
		@endforelse
	</div>
</div>
@if (isset($post['rewrite']['paged']))
	{!! $post['pagination'] !!}
@endif
@endsection