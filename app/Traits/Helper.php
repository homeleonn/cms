<?php

namespace App\Traits;

trait Helper
{
	private function i()
	{
		$this->definedVar('routeIndex');
		
		return redirect()->route($this->routeIndex);
	}
	
	private function view($name, $args)
	{
		$this->definedVar('viewRoot');
		
		return view($this->viewRoot . '.' . $name, $args);
	}
	
	private function definedVar($varName)
	{
		if (!isset($this->$varName)) {
			throw new \Exception("{$varName} is not defined");
		}
	}
}