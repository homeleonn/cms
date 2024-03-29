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
		return rtrim(str_repeat('?,', count($arr)), ',');
	}
	
	
	
	public static function arrayInCode($array, $arrayName = null, $level  = 0) 
	{
		$tabs = str_repeat("\t", $level + 1);
		$code = '';
		$code .= (!$level ? ($arrayName ? '$' . $arrayName . ' = ' : 'return ') . '[' : "[\n");
		
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				if ($level < 1) {
					$code .= "\n";
				}
				
				$code .= "{$tabs}'{$key}' => ";
				$code .= arrayInCode($value, $arrayName, $level + 1);
			} else {
				if ($level == 0) {
					$code .= "\n";
				}
				
				$code .= "{$tabs}'{$key}' => '{$value}',\n";// . ($level ? "\n" : "");
			}
			
		}
		if ($level > 1) {
			$code .= str_repeat("\t", $level - 1);
		}
		
		$code .= (!$level ? "\n" : '') . ($level ? "\t]" : "]");
		$code .= !$level ?  ';' : ',';
		
		return $code;
	}
	
	
	public static function builtHierarchyDown(&$itemsOnId, $current, $mergeKey, $level = 0)
	{
		if ($level > 10) exit('stop recursion');
		$hierarchy = '';
		if (isset($itemsOnId[$current->parent][0])) {
			$next = $itemsOnId[$current->parent][0];
			$hierarchy = self::builtHierarchyDown($itemsOnId, $next, $mergeKey, $level + 1) . '|' . $next[$mergeKey];
		}
		return $hierarchy;
	}
	
	public static function builtHierarchyUp($itemsOnParent, $current, $postTermsOnId, $mergeKey, $level = 0)
	{
		if($level > 10) exit('stop recursion');
		$hierarchy = '';
		
		if(isset($itemsOnParent[$current->id])){
			foreach($itemsOnParent[$current->id] as $possibleNext){
				if($possibleNext['parent'] == $current->id && isset($postTermsOnId[$possibleNext['id']])){
					$next = $possibleNext;
				}
			}
			if(isset($next))
				$hierarchy = $next[$mergeKey] . '|' . self::builtHierarchyUp($itemsOnParent, $next, $mergeKey, $level + 1);
		}
		return $hierarchy;
	}
	
	public static function column($arr, $columnName)
	{
		foreach ($arr as $a) $res[] = $a[$columnName];
		return $res;
	}
	
	public static function termsHTML($taxonomies, $archive)
	{
		if (!is_array($taxonomies)) {
			return false;
		}
		
		$html = '';
		
		foreach ($taxonomies as $taxName => $terms) {
			$html .= "<li>{$taxName}:";
			
			foreach ($terms as $termName => $termLink) {
				$html .= " <a href='". url('/') . '/' . $archive . "/{$termLink}'>{$termName}</a>,";
			}
			
			$html = rtrim($html, ',') . '</li>';
		}
		
		return '<ul class="terms">' . $html . '</ul>';
	}
	
	public static function archiveTermsHTML($taxonomies, $archive)
	{
		if(!is_array($taxonomies)) {
			return false;
		}
		
		$html = '';
		
		foreach($taxonomies as $taxName => $terms){
			$html .= '<div class="filters"><div class="title">' . $taxName . '</div><div class="content">';
			
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". url('/') . '/' . $archive . "/{$termLink}'>{$termName}</a>";
			}
			
			$html .= " <a href='". url('/') . '/' . $archive . "'>Все</a></div></div>";
		}
		
		return $html;
	}
	
	public static function toArray($var)
	{
		return json_decode(json_encode($var), true);
	}
	
	public static function push(&$array, $key, $value)
	{
		if (is_null($key)) {
			return $array = $value;
		}

		$keys = explode('.', $key);
		
		if (true) {
			Arr::pushInfinity($array, $keys, $value);
		} else {
			Arr::pushLimited($array, $keys, $value);
		}
		
		return $array;
	}

	private static function pushInfinity(&$array, $keys, $value) {
		while (count($keys) > 1) {
			$key = array_shift($keys);
			
			if (! isset($array[$key]) || ! is_array($array[$key])) {
				$array[$key] = [];
			}

			$array = &$array[$key];
		}

		$array[array_shift($keys)] = $value;
		
		return $array;
	}

	private static function pushLimited(&$array, $keys, $value) {
		$countKeys = count($keys);
		if ($countKeys == 1) {
			$array[$keys[0]] = $value;
		} elseif ($countKeys == 2) {
			$array[$keys[0]][$keys[1]] = $value;
		} elseif ($countKeys == 3) {
			$array[$keys[0]][$keys[1]][$keys[2]] = $value;
		} elseif ($countKeys == 4) {
			$array[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
		}
		
		return $array;
	}
	
	public static function getValuesRecursive($original)
	{
		array_walk_recursive($original, function ($value) use (&$destination) {
			$destination[] = $value;
		});
		
		return $destination;
	}
	
	public static function getKeys($array, $key, $distinct = false){
		$k = [];
		
		foreach ($array as $a) {
			
			// if (!is_array($a)) {
				// $a = (array)$a;
			// }
			
			if ($distinct) {
				if (!isset($k[$a[$key]])) {
					$k[$a[$key]] = $a[$key];
				}
			} else {
				$k[] = $a[$key];
			}
		}
		
		return $k;
	}
	
	
	public static function clearHtmlKeysValues($fields)
	{
		if (!is_array($fields)) {
			$fields = [$fields];
		}
		
		foreach ($fields as $key => $field) {
			$fields[htmlspecialchars($key)] = htmlspecialchars($field);
		}
		
		return $fields;
	}
	
	
	public static function replaceByPlaceholders($count)
	{
		if (is_array($count)) {
			$count = count($count);
		}
		
		return rtrim(str_repeat("?,", $count), ',');
	}
}