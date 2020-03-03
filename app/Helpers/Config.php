<?php

namespace App\Helpers;

use App\Traits\{Singletone, Container};

class Config
{
	use Singletone, Container;
	
	public static function optionsLoad()
	{
		self::$container = array_merge(self::$container, require base_path() . '/options.php');
	}
	
	public static function getBreadcrumbs()
	{
		$bc 		= self::get('breadcrumbs');
		$bcCount 	= count($bc);
		$i 			= 0;
		$html 		= '<div id="breadcrumbs">';
		
		foreach ($bc as $url => $name) {
			$html .= ++$i == $bcCount ? $name : '<a href="'.$url.'">'.$name.'</a> > ';
		}
		
		return $html . '</div>';
	}
}