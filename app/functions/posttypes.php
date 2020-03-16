<?php

use App\Helpers\PostsTypes;


if (!function_exists('isAdminSide')) {
	function isAdminSide() {
		return isset($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/admin') === 0);
	}
}

addPageType('post', [
		'type' => 'post',
		'title' => 'Блог',
		'h1' => 'Блог',
		'title_for_admin' => 'Записи',
		'description' => 'Блог',
		'add' => 'Добавить запись',
		'edit' => 'Редактировать запись',
		'delete' => 'Удалить запись',
		'common' => 'записей',
		'hierarchical' => false,
		'has_archive'  => 'blog',
		'taxonomy' => [
			'category' => [
				'title' => 'Категория',
				'add' => 'Добавить категорию',
				'edit' => 'Редактировать категорию',
				'delete' => 'Удалить категорию',
				'hierarchical' => false,
			],
			// 'age' => [
				// 'title' => 'Возрастная категория',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
		],
		'rewrite' => ['slug' => 'blog/%category%', 'with_front' => false, 'paged' => 20],
]);



addPageType('page', [
		'type' => 'page',
		'title' => '',
		'title_for_admin' => 'Страницы',
		'description' => 'Страницы',
		'add' => 'Добавить страницу',
		'edit' => 'Редактировать страницу',
		'delete' => 'Удалить страницу',
		'common' => 'страниц',
		'hierarchical' => true,
		'has_archive'  => false,
		'taxonomy' => [],
		'rewrite' => ['slug' =>'', 'with_front' => true, 'paged' => false],
]);

addPageType('program', [
		'type' => 'program',
		'title' => 'Программы',
		'_seo_title' => 'Детские аниматоры на день рождения Одесса, утренник, выпускной. Пригласить аниматора для ребенка',
		'h1' => 'Аниматоры, шоу программы на детский праздник в Одессе',
		'title_for_admin' => 'Программы',
		'description' => 'Заказать детского аниматора на день рождения ребенка на дом либо на утренник или выпускной в Одессе, широкий выбор аниматоров и шоу программы, а так же красочные детские ведущие, которые порадуют детей интересными конкурсами и подарят массу ярких впечатлений. Все останутся довольны. Заказать аниматора для ребенка на праздник. Цена',
		'add' => 'Добавить программу',
		'edit' => 'Редактировать программу',
		'delete' => 'Удалить программу',
		'common' => 'программ',
		'hierarchical' => false,
		'has_archive'  => 'programs',
		'rewrite' => ['slug' => 'programs', 'with_front' => false, 'paged' => 5],
		// 'taxonomy' => [
			// 'age' => [
				// 'title' => 'Возрастная категория',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
			// 'gen' => [
				// 'title' => 'Пол ребенка',
				// 'add' => 'Добавить возрастную категорию',
				// 'edit' => 'Редактировать возрастную категорию',
				// 'delete' => 'Удалить возрастную категорию',
				// 'hierarchical' => true,
			// ],
		// ]
]);

addPageType('service', [
		'type' => 'service',
		'title' => 'Доп. услуги',
		'_seo_title' => 'Дополнительные услуги | Funkids',
		'h1' => 'Дополнительные услуги',
		'title_for_admin' => 'Доп. услуги',
		'description' => 'Дополнительные услуги на детский праздник, мыльные пузыри, сладкая вата, всё что бы разнообразить праздничный день, запоминающиеся мгновения жизни ребенка | FunKids',
		'add' => 'Добавить услугу',
		'edit' => 'Редактировать услугу',
		'delete' => 'Удалить услугу',
		'common' => 'услуг',
		'hierarchical' => false,
		'has_archive'  => 'services',
		'rewrite' => ['slug' => 'services', 'with_front' => false, 'paged' => 20],
]);

addPageType('review', [
		'type' => 'review',
		'title' => 'Отзывы',
		'_seo_title' => 'Отзывы | Funkids',
		'h1' => 'Отзывы наших клиентов',
		'title_for_admin' => 'Отзывы',
		'description' => 'Отзывы | FunKids',
		'add' => 'Добавить отзыв',
		'edit' => 'Редактировать отзыв',
		'delete' => 'Удалить отзыв',
		'common' => 'Отзывы',
		'hierarchical' => false,
		'has_archive'  => 'reviews',
		'rewrite' => ['slug' => 'reviews', 'with_front' => false, 'paged' => 20],
]);


function addPageType(string $type, array $options){
	PostsTypes::set($type, $options);
	
	if (!isAdminSide() || !isset($_SERVER['REQUEST_URI'])) {
		$pc = 'App\Http\Controllers\PostController';
		$sep = '/';
		$paged = $options['rewrite']['paged'] ? "{$sep}page/{page}" : '';
		
		if ($options['has_archive']) {
			Route::get($options['has_archive'] . '/{slug}', function($slug) use ($type, $pc){
				return App::make($pc)->run($type, 'actionSingle', [$slug]);
			});
		}
		
		if ($options['has_archive']) {
			Route::get($options['has_archive'] . $paged, function($page) use ($type, $pc){
				return App::make($pc)->run($type, 'actionList', [$page]);
			});
			Route::get($options['has_archive'], function() use ($type, $pc){
				return App::make($pc)->run($type, 'actionList', [1]);
			});
		}
		
		if (!empty($options['taxonomy'])) {
			if ($options['has_archive'] === false) $sep = '';
			foreach ($options['taxonomy'] as $t => $values) {
				Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}", function($tslug) use ($type, $pc, $t){
					return App::make($pc)->run($type, 'actionList', [1, $t, $tslug]);
				});
				Route::get("{$options['has_archive']}{$sep}{$t}/{tslug}{$paged}", function($tslug, $page) use ($type, $pc, $t){
					return App::make($pc)->run($type, 'actionList', [$page, $t, $tslug]);
				});
			}
		}
	}
	
	
	if (isAdminSide() || !isset($_SERVER['REQUEST_URI'])) {
		Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function() use ($type){
			Route::get($type, 'PostController@actionIndex')->name("{$type}.index");
			Route::post($type, 'PostController@actionStore')->name("{$type}.store");
			Route::get($type.'/create', 'PostController@actionCreate')->name("{$type}.create");
			// Route::get($type.'/{post}', 'PostController@actionShow')->name("{$type}.show");
			Route::put($type.'/{post}', 'PostController@actionUpdate')->name("{$type}.update");
			Route::delete($type.'/{post}', 'PostController@actionDestroy')->name("{$type}.destroy");
			Route::get($type.'/{post}/edit', 'PostController@actionEdit')->name("{$type}.edit");
			
			
			Route::get($type . '/term', 'PostController@actionTermIndex')->name("{$type}.term_index");
			Route::post($type . '/term', 'PostController@actionTermStore')->name("{$type}.term_store");
			Route::get($type . '/create-term', 'PostController@actionTermCreate')->name("{$type}.term_create");
			// Route::get($type . '/term/{term}', 'PostController@actionTermShow')->name("{$type}.term_show");
			Route::put($type . '/term/{term}', 'PostController@actionTermUpdate')->name("{$type}.term_update");
			Route::delete($type . '/term/{term}', 'PostController@actionTermDestroy')->name("{$type}.term_destroy");
			Route::get($type . '/term/{term}/edit', 'PostController@actionTermEdit')->name("{$type}.term_edit");
		});
	}
				
	
	
}