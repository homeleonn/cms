<?php

namespace App\Traits;

trait Container
{
	private static $container = [];
	
	public static function get($key)
	{
		if (!self::has($key)) {
			throw new \Exception("Element '{$key}' not found");
		}
		
		return self::$container[$key];
	}
	
	public static function set($key, $value)
	{
		self::$container[$key] = $value;
	}
	
	public static function has($key)
	{
		return isset(self::$container[$key]);
	}
	
	public static function getAll()
	{
		return self::$container;
	}
}