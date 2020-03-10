<?php
/**
 *  Template: price
 */
?>
@extends('funkids.layout')
@section('content')
<h1 class="section-title">Цены</h1>

<div class="table-wrapper">
<table border="1" class="pricetable">
	<tr>
		<th colspan="4">Стандартная программа</th>
	</tr>
	<tr>
		<th>Продолжительность</th>
		<th>Встреча</th>
		<th>Количество<br> персонажей</th>
		<th>Стоимость</th>
	</tr>
	<tr>
		<td rowspan="3">1 час</td>
		<td rowspan="3">700</td>
		<td>1</td>
		<td>900</td>
	</tr>
	<tr>
		<td>2</td>
		<td>1400</td>
	</tr>
	<tr>
		<td>Каждый последующий</td>
		<td> + 500</td>
	</tr>
</table>
</div>

<div class="table-wrapper">
<table border="1" class="pricetable">
	<tr>
		<th colspan="6">Нестандартные программы</th>
	</tr>
	<tr>
		<th>Программа</th>
		<th>Продолжительность</th>
		<th>Встреча</th>
		<th>Количество<br> персонажей</th>
		<th>Стоимость</th>
		<th>+ персонаж</th>
	</tr>
	<tr>
		<td>Железный человек</td>
		<td>1 час</td>
		<td>1000</td>
		<td>2</td>
		<td>1700</td>
		<td rowspan="5">500</td>
	</tr>
	<tr>
		<td>Оптимус Прайм</td>
		<td>1 час</td>
		<td>1400</td>
		<td>2</td>
		<td>2000</td>
	</tr>
	<tr>
		<td rowspan="2">Танцевальная программа,<br>Футбольная программа</td>
		<td rowspan="2">1 час</td>
		<td rowspan="2"></td>
		<td>1</td>
		<td>1000</td>
	</tr>
	<tr>
		<td>2</td>
		<td>1400</td>
	</tr>
	<tr>
		<td>Майнкрафт</td>
		<td colspan="4">
			<ul class="disc" style="padding-left: 10px;">
				<li>Ведущий и Ростовая кукла на выбор (Стив или Крипер) – 1400 грн</li>
				<li>Ведущий и Две ростовые куклы ( Стив и Крипер) – 1800 грн</li>
			</ul>
		</td>
	</tr>
</table>
</div>

<div class="table-wrapper">
<table border="1" class="pricetable">
	<tr>
		<th colspan="4">Дополнительные услуги</th>
	</tr>
	<tr>
		<th>Название</th>
		<th>Цена</th>
	</tr>
	<tr>
		<td>Научное шоу</td>
		<td>
			<ul class="disc" style="padding-left: 10px;">
				<li>1 человек 1000 грн</li>
				<li>2 человека 1300 грн</li>
			</ul>
		</td>
	</tr>
	<tr>
		<td>Шоу мыльных пузырей</td>
		<td>20-30 минут 800 грн</td>
	</tr>
	<tr>
		<td>Сладкая вата</td>
		<td>800 грн</td>
	</tr>
	<tr>
		<td>Аквагрим</td>
		<td>1 час 600 грн</td>
	</tr>
	<tr>
		<td>Генератор мыльных пузырей</td>
		<td>200 грн</td>
	</tr>
	<tr>
		<td>Дым машина</td>
		<td>200 грн</td>
	</tr>
	<tr>
		<td>Пиньята</td>
		<td>Под заказ (уточняйте по телефону)</td>
	</tr>
	<tr>
		<td>Бумажное шоу</td>
		<td>
			<ul class="disc" style="padding-left: 10px;">
				<li>30 кг новой бумаги 3500 за последующие 10 кг по 1000 грн.</li>
				<li>30 кг второсортной 1800 за след. 10 кг по 400 грн.</li>
			</ul>
		</td>
	</tr>
</table>
</div>

<noindex>
<div class="container">

		<?//=funkids_inProgram()?>
	
	<div>
		<p>
		Программа состоит из более чем десятка пунктов. Конкурсы подбираются под каждую уникальную группу детей по возрасту и количеству.
		</p>
		<p>Приятные незабываемые впечатления гарантированы. Наши профессионалы по детским праздникам устроят потрясающее шоу для малышей.</p>
		<b>Детей и родителей ждут</b>:
		<ul class="disc" style="margin-left: 30px;">
			<li>Шоу программы и представления</li>
			<li>Интересные конкурсы</li>
			<li>Веселая и атмосферная музыка</li>
			<li>Тематический реквизит и красочные костюмы аниматоров</li>
		</ul>
		
		<b>Места для праздников могут быть разнообразными:</b>
			<ul class="disc" style="margin-left: 30px;">
				<li>Детский сад или школа</li>
				<li>Кафе</li>
				<li>На природе</li>
			</ul>
		<p><b>Доступные цены. Аниматоры и детские шоу программы давно перестали быть роскошью.</b></p>
		
		<p>Стоимость услуг на сайте указана ориентировочная. Детали и окончательную цену необходимо обсудить с нашим менеджером.</p>
	</div>
</div>
<div class="wave1" style="margin: 20px 0;">
<?=funkids_readyToHolyday()?>
</div>
</noindex>
@endsection