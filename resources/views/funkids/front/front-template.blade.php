<?php
/**
 *  Template: front
 */
?>
@extends('layout')

@section('content')
<div class="popular">
	<div class="header-img">
		<div class="back-img"></div>
		<h1 class="center">Праздник для детей от детских аниматоров в Одессе. Организация «под ключ»</h1>
		<div class="logo-text">
			<span class="upper"><div class="first-word">FunKids</div></span>
		</div>
	</div>
	{{funKids_getBlock('programs')}}
<?php
//funKids_popular();
?>
</div>
{!! funKids_getBlock('services') !!}
<div class="s-about" id="s-about">
	<div class="girl-left sprite"></div>
	<h2 class="section-title">О нас</h2>
	<div class="container">
		<h3 class="inline-title center">Почему выбирают именно нас?</h3>
		<p>
			Организаторы детских праздников FunKids Одесса - это профессионалы своего дела - Аниматоры, которые вкладывают большой труд не только на выступлениях, но и в постоянной работе над самими собой. Яркие костюмы и реквизит, интересные шоу программы, улыбки на лицах Ваших детей, незабываемые впечатления, а самое главное это большое удовольствие, радость и неизгладимые впечатление, которые мы приносим деткам и их родителям. Мы всегда готовы помочь в выборе заведения для проведения Вашего праздника. Работаем по всей Одессе и за её пределами.
		</p>
		
		<h2 class="inline-title center">Детские аниматоры в Одессе, шоу программы на праздник</h2>
		<p>День рождения ребенка? Утренник или может быть красочный выходной день. Наши опытные аниматоры составят компанию Вашему малышу, оставят после себя массу положительных эмоций и красочных воспоминаний. Найдут подход к каждому ребенку, праздник будет сказочным и веселым, детей окружат герои мультфильмов в потрясающих костюмах. У нас есть большой выбор персонажей для детских развлекательных утренников или выпускных, множество ярких костюмов аниматоров удовлетворят любое желание ребенка. Восторг детей и их родителей постоянно присутствует на празднованиях рядом с нашими аниматорами и их шоу программами.</p>
		
		<h2 class="inline-title center">Аниматор на детский день рождения - мечта ребенка</h2>
		<p>Наши аниматоры станут лучшими друзьями для Вашего ребенка на его дне рождения, ведь этот день призван оставлять положительные эмоции и яркие воспоминания, Бэтмен встанет на защиту важного праздника, Супермен окажется неуязвимым и Ваш ребенок будет в восторге от создания праздничной атмосферы, Маша и медведь развеселят и поведут за собой в сказочный мир игр, активных конкурсов и приятных бесед!</p> 
		
		<p>У нас есть всё для проведения дня рождения ребенка, веселые аниматоры, яркие костюмы, интересные конкурсы состоящие из множества различных сценариев, которые буду захватывать дух ребенка каждое мгновение памятного дня, генератор мыльных пузырей, или научное шоу не оставят равнодушными никого.</p>
		
		<p>
			Задаваясь вопросом: куда сводить детей на праздники или устроить праздник для ребенка на детский день рождения пригласив аниматоров на дом, в детский сад, школу или кафе, знайте, у нас есть огромный выбор всевозможных развлекательных программ в которых участвуют не только дети, но и взрослые. Украсим детский утренник или выпускной. В каждой программе есть свой тематический реквизит и красочные декорации, она насыщена развлекательными и танцевальными конкурсами. Изюминка наших аниматоров - хорошие танцоры и акробаты.
		</p>
		
		<p>
			Устроить детский праздник это по нашей части, это наш конек организация детских праздников для детей любого возраста, наше призвание. Мы переносим деток в волшебную сказку, устраивая наши шоу программы уже много лет. Дети часто вспоминают это веселое время и зовут нас снова. Теплые слова благодарности и отзывы родителей греют нас и мотивируют на дальнейшее развитие, создание большего ряда детских персонажей и интересных сценариев к празднику.
		</p>
		
		<div class="inline-title-margin">В детскую шоу программу включено:</div>
		<div class="col-sm-12">
			<ul class="my">
				<li>Красочные костюмы</li>
				<li>Интерактивная программа состоящая из более чем десятка пунктов</li>
				<li>Тематический реквизит</li>
				<li>Музыкальное сопровождение</li>
				<li>Диджей</li>
			</ul>
		</div>
	</div>
</div>
<noindex>
{!! funkids_readyToHolyday() !!}
{!! funKids_getBlock('reviews') !!}
</noindex>
@endsection