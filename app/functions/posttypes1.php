<?php

use App\Helpers\PostsTypes;

$postTypes = [
	'post' => [
		'name' => 'Статьи',
		'type' => 'post',
		'archive' => 'blog',
	],
	
	'page' => [
		'name' => 'Страницы',
		'type' => 'page',
		'hierarchical' => true,
		'archive' => '',
	],
];

// dd($postTypes);
// dd(serialize($postTypes));

// dd(unserialize('a:6:{i:5;a:3:{s:4:"name";s:6:"qwerty";s:4:"type";s:7:"qwerty1";s:7:"archive";s:7:"qwertys";}i:4;a:3:{s:4:"name";s:14:"Отзывы12";s:4:"type";s:6:"review";s:7:"archive";s:7:"reviews";}i:3;a:3:{s:4:"name";s:20:"Доп. услуги";s:4:"type";s:7:"service";s:7:"archive";s:8:"services";}i:2;a:3:{s:4:"name";s:18:"Программы";s:4:"type";s:7:"program";s:7:"archive";s:8:"programs";}i:1;a:4:{s:4:"name";s:16:"Страницы";s:4:"type";s:4:"page";s:7:"archive";N;s:12:"hierarchical";s:2:"on";}i:0;a:3:{s:4:"name";s:12:"Статьи";s:4:"type";s:4:"post";s:7:"archive";s:5:"posts";}}'));

// $postTypes = [
	// [
		// 'name' => 'Статьи',
		// 'type' => 'post',
		// 'archive' => 'posts',
	// ],
	// [
		// 'name' => 'Страницы',
		// 'type' => 'page',
		// 'hierarchical' => true,
		// 'archive' => '',
	// ],
	// [
		// 'name' => 'Программы',
		// 'type' => 'program',
		// 'archive' => 'programs',
	// ],
	// [
		// 'name' => 'Доп. услуги',
		// 'type' => 'service',
		// 'archive' => 'services',
	// ],
	// [
		// 'name' => 'Отзывы',
		// 'type' => 'review',
		// 'archive' => 'reviews',
	// ],
// ];

// dd(Options::get('posttypes', true));

// dd($postTypes, implode('|', array_column($postTypes, 'archive')));

// d(Options::get('posttypes', true));
// dd(array_merge($postTypes, Options::get('posttypes', true)));




$postTypes = array_merge($postTypes, Options::get('posttypes', true));
//dd($postTypes);
Options::set('posttypes_all', $postTypes);
$validArchives = '';



foreach ($postTypes as $value) {
	if ($value['archive']) {
		$validArchives .= $value['archive'] . '|';
	}
}
//dd($validArchives);

$validArchives = rtrim($validArchives, '|');
// dd(Options::get('posttypes', true));

Route::get('/{archive}{page?}', 'App\Http\Controllers\PostController@archiveWithoutTerm')->where([
	'archive' => $validArchives,
	'page' => '/page/([2-9]|\d{2,})',
]);

Route::get('/{archive}/{term}{page?}', 'App\Http\Controllers\PostController@archiveWithTerm')->where([
	'archive' => $validArchives,
	'term' => '(category/[a-zA-Z0-9\-]+|tag/[a-zA-Z0-9\-\+]+)',
	'page' => '/page/([2-9]|\d{2,})',
	]);