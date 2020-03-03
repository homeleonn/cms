<?//=dd(get_defined_vars());?>
@extends('layout')

@section('content')
<div class="list-wrapper container imgw100">
	<h1>{{$post['h1'] ?? ''}}</h1>
	@if ($post['type'] == 'program')
		<div class="col-sm-12 flex line programs">
			@forelse ($post['__list'] as $item)
				<div class="col-sm-4 list-item center">
					<div>
						<a href="{{ $item->slug }}">	
							<img src="{{ postImgSrc($item, 'medium') }}"  alt="{{ $item->title }} - шоу программа">
							<div class="itemcontent">
								<div class="inline-title">{{ $item->short_title ?: $item->title }}</div>
							</div>
						</a>
					</div>
				</div>
			@empty
				<p>Архивов нет!</p>
			@endforelse
		</div>
		{!! $post['pagination'] !!}
		<h2 class="inline-title center">Детские аниматоры Одесса, шоу программы на праздник</h2>
		<p><strong>День рождения ребенка?</strong> Утренник или может быть красочный выходной день. Наши опытные <strong>аниматоры</strong> составят компанию Вашему малышу, оставят после себя массу положительных эмоций и красочных воспоминаний. Найдут подход к каждому ребенку, праздник будет сказочным и веселым, детей окружат герои мультфильмов в потрясающих костюмах. У нас есть большой выбор персонажей для детских развлекательных утренников или выпускных, множество ярких костюмов аниматоров удовлетворят любое желание ребенка. Восторг детей и их родителей постоянно присутствует на празднованиях рядом с нашими аниматорами и их шоу программами.</p>
		
		<h2 class="inline-title center">Аниматор на день рождения - мечта ребенка</h2>
		<p>Наши аниматоры станут лучшими друзьями для Вашего ребенка на его дне рождения, ведь этот день призван оставлять положительные эмоции и яркие воспоминания, Бэтмен встанет на защиту важного праздника, Супермен окажется неуязвимым и Ваш ребенок будет в восторге от создания праздничной атмосферы, Маша и медведь развеселят и поведут за собой в сказочный мир игр, активных конкурсов и приятных бесед!</p> 
		
		<p>У нас есть всё для проведения дня рождения ребенка, веселые аниматоры, яркие костюмы, интересные конкурсы состоящие из множества различных сценариев, которые буду захватывать дух ребенка каждое мгновение памятного дня, генератор мыльных пузырей, или научное шоу не оставят равнодушными никого.</p>
		
		<h3 class="inline-title center">В шоу программу аниматоров на детский праздник входят:</h3>
		
		<noindex>
		<div class="row">
			<div class="col-sm-6">
				<ul class="my">
					<li>Костюмы</li>
					<li>Интерактивная программа</li>
					<li>Тематический реквизит</li>
				</ul>
			</div>
			<div class="col-sm-6">
				<ul class="my">
					<li>Музыкальное сопровождение</li>
					<li>Диджей</li>
				</ul>
			</div>
		</div>
		</noindex>

	@elseif ($post['type'] == 'service'):
		<div class="col-sm-12 flex extra-services ">
		@forelse ($post['__list'] as $item)
			<div class="col-sm-4 list-item center">
				<div>
					<a href="{{ $item->slug }}">	
						<div class="img1"><img src="{{ postImgSrc($item, 'medium') }}"  alt="{{ $item->short_title ?: $item->title }} - дополнительная услуга к детскому празднику на день рождения"></div>
						<div class="itemcontent">
							<div class="inline-title">{{ $item->short_title ?: $item->title }}</div>
						</div>
					</a>
				</div>
			</div>
		@empty
			<p>Архивов нет!</p>
		@endforelse
		</div>
	{!! $post['pagination'] !!}
	@else
		<div class="filters-inline">{{ $filters ?? '' }}</div>
		<div class="col-sm-12 news">
		@forelse ($post['__list'] as $item)
			<div class="item clearfix">
				<div class="ncontent" >
						<img src="{{ postImgSrc($item, 'medium') }}" alt="{{ $item['title'] }}" style="height: 320px; width: 320px;object-fit: cover;" class="floatimg">
					<div class="title" >
						<a href="{{ $item['slug'] }}">
							<span class="inline-title">{{ $item['short_title']?:$item['title'] }}</span>
						</a>
					</div>
					<span>
						{{ funkids_clearTags(mb_substr($item['content'], 0 ,500)).'...' }}
					</span>
					<div class="right"><a href="{{ $item['slug'] }}" class="button">Читать</a></div>
				</div>
			</div>
		@empty
			<p>Архивов нет!</p>
		@endforelse
		</div>
		{!! $post['pagination'] !!}
	@endif
		{{ doAction('after_show_list', $post) }}
</div>
@endsection