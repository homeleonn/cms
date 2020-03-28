<?php

namespace App\Helpers;

use Options;

class Cache
{
	private static $uploadsDir;
	
	public static function get($cacheFileName = null, $delay = 86400, $outNow = true){
		if (!Options::get('cache_enable')) {
			return false;
		}
		
		// dd(debug_backtrace(null, 3));
		// d($cacheFileName);
		$cacheFileName = self::setCacheDir($cacheFileName);
		// dd($cacheFileName);
		
		if (file_exists($cacheFileName))
		{
			if ($delay == -1 || (filemtime($cacheFileName) > time() - $delay)) {
				if (($data = file_get_contents($cacheFileName)) === FALSE) {
					return false;
				}
				
				if ($data != '') {
					if ($outNow) {
						echo $data;
					} else {
						return $data;
					}   
					
					return true;
				}
			}
		}
		
		ob_start();
		
		return false;
	}

	public static function set($cacheFileName = null, $data = null)
	{
		if (!Options::get('cache_enable')) {
			return false;
		}
		
		if (!$data) {
			$data = ob_get_clean();
		}
		
		$cacheFileName = self::setCacheDir($cacheFileName);
		
		if (!is_dir($dir = dirname($cacheFileName))) {
			mkdir($dir, 0755, true);
		}
		
		file_put_contents($cacheFileName, $data, LOCK_EX);
		return $data;
	}
	
	public static function clear($cacheFileName)
	{
		$cacheFileName = self::setCacheDir($cacheFileName);
		
		if(is_file($cacheFileName)){
			unlink($cacheFileName);
		}
	}
	
	private static function setCacheDir($fileName = null) 
	{
		if (!self::$uploadsDir) {
			self::$uploadsDir = cache_dir();
		}
		
		return self::createCurrentCacheFileName($fileName);
	}
	
	private static function createCurrentCacheFileName($fileName = null)
	{
		return self::$uploadsDir . ($fileName ?? md5(requestUri()))  . '.html';
	}
}