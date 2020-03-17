<?php

namespace App\Helpers;

class Options
{
	private $options = [];
	private $dinamic = ['optionsLoad'];
	private $started = false;
	private $optionsPath;
	
	public function __construct(string $optionsPath)
	{
		if ($this->started) return false;
		
		if (!file_exists($optionsPath)) {
			throw new \Exception('Options file not exists');
		}
		
		$this->started 		= true;
		$this->optionsPath 	= $optionsPath;
		$this->options 		= array_merge($this->options, require $this->optionsPath);
	}
	
	public function set(string $key, $value): void
	{
		$this->options[$key] = $value;
	}
	
	public function push(string $key, $value): void
	{
		Arr::push($this->options, $key, $value);
	}
	
	public function get(string $key, $decode = false)
	{
		if (!$this->has($key)) {
			// throw new \Exception("Element '{$key}' not found");
			return null;
		}
		
		return $decode ? unserialize($this->options[$key]) : $this->options[$key];
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