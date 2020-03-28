<?php

namespace App\Helpers;

use Exception;

class Options
{
	private $options = [];
	private $started = false;
	private $optionsPath;
	
	public function __construct(string $optionsPath)
	{
		if ($this->started) return false;
		
		if (!file_exists($optionsPath)) {
			throw new Exception('Options file not exists');
		}
		
		$this->started 		= true;
		$this->optionsPath 	= $optionsPath;
		$this->options 		= array_merge($this->options, require $this->optionsPath);
	}
	
	public function set(string $key, $value = null): void
	{
		Arr::push($this->options, $key, $value);
	}
	
	public function push(string $key, $value = null): void
	{
		Arr::push($this->options, $key, $value);
	}
	
	public function get(string $key, $decode = false)
	{
		
		if (strpos($key, '.') === false) {
			if (!$this->has($key)) {
				return null;
			}
			
			$res = $this->options[$key];
		} else {
			$keys 			= explode('.', $key);
			$firstKeyPart 	= array_shift($keys);
			
			if (!isset($this->options[$firstKeyPart])) {
				return null;
			}
			
			$res = $this->options[$firstKeyPart];

			foreach ($keys as $key) {
				if (!isset($res[$key])) {
					return null;
				}
				
				$res = $res[$key];
			}
		}
		
		return $decode ? unserialize($res) : $res;
	}
	
	public function getAll()
	{
		return $this->options;
	}
	
	public function has(string $key): bool
	{
		return isset($this->options[$key]);
	}

	public function save(string $key, $value, bool $encode = false): void
	{
		$options = $this->getSavedOptions();
		$options[$key] = !$encode ? $value : serialize($value);
		$this->set($key, $value);
		$this->saveOptions($options);
	}

	public function delete(string $key): void
	{
		$options = $this->getSavedOptions();
		
		if (isset($options[$key])) {
			unset($options[$key]);
			$this->saveOptions($options);
		}
		
		if (isset($this->options[$key])) {
			unset($this->options[$key]);
		}
	}
	
	private function getSavedOptions(bool $asArray = true)
	{
		return $asArray ? include $this->optionsPath : file_get_contents($this->optionsPath);
	}
	
	private function saveOptions(array $options = null)
	{
		file_put_contents($this->optionsPath, "<?php\n\n" . Arr::arrayInCode($options ?? $this->options));
	}
}