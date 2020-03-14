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
	
	
	
	public static function arrayInCode($array, $arrayName = null, $level  = 0) {
		$tabs = str_repeat("\t", $level + 1);
		$code = '';
		$code .= (!$level ? ($arrayName ? '$' . $arrayName . ' = ' : 'return ') . '[' : "[\n");
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				if ($level < 1) $code .= "\n";
				$code .= "{$tabs}'{$key}' => ";
				$code .= arrayInCode($value, $arrayName, $level + 1);
			} else {
				if ($level == 0) $code .= "\n";
				$code .= "{$tabs}'{$key}' => '{$value}',\n";// . ($level ? "\n" : "");
			}
			
		}
		if ($level > 1) $code .= str_repeat("\t", $level - 1);
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
		
		if(isset($itemsOnParent[$current['id']])){
			foreach($itemsOnParent[$current['id']] as $possibleNext){
				if($possibleNext['parent'] == $current['id'] && isset($postTermsOnId[$possibleNext['id']])){
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
		if(!is_array($taxonomies)) return false;
		$html = '';
		foreach($taxonomies as $taxName => $terms){
			$html .= "<li>{$taxName}:";
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". url('/') . '/' . $archive . "/{$termLink}'>{$termName}</a>,";
			}
			$html = substr($html, 0, -1) . '</li>';
		}
		return '<ul class="terms">' . $html . '</ul>';
	}
	
	public static function archiveTermsHTML($taxonomies, $archive)
	{
		if(!is_array($taxonomies)) return false;
		$html = '';
		foreach($taxonomies as $taxName => $terms){
			$html .= '<div class="filters"><div class="title">' . $taxName . '</div><div class="content">';
			foreach($terms as $termName => $termLink){
				$html .= " <a href='". url('/') . '/' . $archive . "/{$termLink}'>{$termName}</a>";
			}
			$html .= " <a href='". url('/') . '/' . $archive . "'>Все</a>";
			$html .= '</div></div>';
		}
		return $html;
	}
}