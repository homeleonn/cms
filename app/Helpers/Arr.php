<?php

namespace App\Helpers;

class Arr{
	public static function itemsOnKeys($items, $keys)
	{//dd($items, $keys);
		// if(!is_array($items)){
			// throw new \Exception('Argument $items not array');
		// }
		// if(!is_array($keys)){
			// throw new \Exception('Argument $keys not array');
		// }
		$itemsOnKey = [];
		foreach($items as $item){
			$iitem = [];
			foreach($keys as $k => $key){
				// $presence = false;
				// if (is_object($item)) {
					// if (isset($item->$key)) {
						// $iitem[$key] = $item;
					// }
				// } else {
					// $iitem = $item;
				// }
				// dd($iitem);
				if(!isset($item->$key)){
					throw new \Exception('Key \'' . $key . '\' is not exists');
				}
				// dump($k, $key, );
				$itemsOnKey[$k][$item->$key][] = $item;
			}
		}
		if(empty($itemsOnKey)) return false;
		return count($keys) == 1 ? $itemsOnKey[0] : $itemsOnKey;
	}
	
	public static function getCountItemsLikeQuestionsMark($arr)
	{
		return substr(str_repeat('?,', count($arr)), 0, -1);
	}
}