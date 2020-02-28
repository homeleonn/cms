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
}