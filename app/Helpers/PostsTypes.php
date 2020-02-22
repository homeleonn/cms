<?php

namespace App\Helpers;

use App\Traits\{Container, Singletone};

class PostsTypes
{
	use Container, Singletone;
	
	private static $currentType;
	
	public static function setCurrentType(string $type): void
	{
		self::$currentType = $type;
	}
	
	public static function get($option, $type = null)
	{
		if ($type) {
			if (self::has($type)) {
				return self::$container[$type][$option];
			}
		} else {
			return self::$container[self::$currentType][$option] ?? NULL;
		}
	}
	
	public static function getCurrent()
	{
		return self::$container[self::$currentType] ?? NULL;
	}
	
	public static function getAll()
	{
		return self::$container;
	}
	
	public static function getArchiveSlug()
	{
		return self::getCurrent()['has_archive'];
		return $options['has_archive'] . ($options['has_archive'] ? '/' : '');
	}
}