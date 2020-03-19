<?php

namespace App\Helpers;

use App\Traits\Singletone;

class PostsTypes
{
	use Singletone;
	
	private static $container = [];
	private static $currentType;
	
	public static function setCurrentType(string $type): void
	{
		self::$currentType = $type;
		\View::share('postOptions', self::getCurrent());
	}
	
	public static function set($key, $value)
	{
		$value['type'] = $key;
		self::$container[$key] = $value;
	}
	
	public static function has($key)
	{
		return isset(self::$container[$key]);
	}
	
	public static function get($option = false, $type = null)
	{
		if ($type) {
			if (self::has($type)) {
				return $option ? self::$container[$type][$option] : self::$container[$type];
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
	
	public static function checkTaxonomyExists($taxonomy, $redirect = false)
	{
		$exists = true;
		
		if (isset($current['taxonomy'])) {
			$exists = false;
		} else {
			if (!is_array($taxonomy)) {
				$taxonomy = [$taxonomy];
			}
			
			$current = self::getCurrent();
			
			foreach ($taxonomy as $t) {
				if (! isset($current['taxonomy'][$t])) {
					$exists = false;
					break;
				}
			}
		}
		
		if (!$exists && $redirect) {
			redirBack('Ошибка таксономии');
		}
		
		return $exists;
	}
}