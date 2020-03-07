<?php

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


addPageType('test', [
		'title' => 'Test',
		'description' => 'Test Description',
		'h1' => 'Test',
		'hierarchical' => false,
		'has_archive'  => 'tests',
		'rewrite' => ['slug' => 'tests', 'with_front' => false, 'paged' => 20],
		'taxonomy' => [
			'newscat' => [
				'title' => 'Возрастная категория',
				'add' => 'Добавить возрастную категорию',
				'edit' => 'Редактировать возрастную категорию',
				'delete' => 'Удалить возрастную категорию',
				'hierarchical' => true,
			],
		]
]);

addPageType('program', [
		'title' => 'Программы',
		'description' => 'Programs Description',
		'h1' => 'Программы',
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