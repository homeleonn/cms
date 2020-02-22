<?php

namespace App\Traits;

trait Container
{
	private static $container = [];
	
	public static function get($key)
	{
		return self::has($key) ? self::$container[$key] : NULL;
	}
	
	public static function set($key, $value)
	{
		self::$container[$key] = $value;
	}
	
	public static function has($key)
	{
		return isset(self::$container[$key]);
	}
}