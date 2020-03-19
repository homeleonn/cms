<?php

namespace App\Helpers;

class Transliteration{
	public static function run($from){
		if(!is_string($from)){
			throw new \Exception('Type error: variable from is not string');
		}
		
		$ru = ['щ','ш','ч','ц','ю','я','ё','ж','ъ','ы','э','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ь', ' '];
		$en = ['shh','sh','ch','cz','yu','ya','yo','zh','yi','ui','e','a','b','v','g','d','e','z','i','j','k','l','m','n','o','p','r','s','t','u','f','x','','-'];
		$from = mb_strtolower($from);
		
		foreach($ru as $key => $symbol){
			$from = str_replace($symbol, $en[$key], $from);
		}
		$newUrl = '';
		if(!preg_match('/^' . URL_PATTERN . '$/', $from)){
			$i = 0;
			do{
				if(preg_match('/' . URL_PATTERN . '/', $from{$i})){
					if(mb_detect_encoding($from{$i}))
						$newUrl .= $from{$i};
				}else{
					$newUrl .= '-';
				}
				$i++;
			}while(isset($from{$i}));
			if(!$newUrl || !preg_match('/^' . URL_PATTERN . '$/', mb_convert_encoding($newUrl, 'UTF-8'))) $newUrl = '1';
		}
		$newUrl = $newUrl ? $newUrl : $from;
		$newUrl = preg_replace('/-+/', '-', $newUrl);
		return $newUrl ? $newUrl : $from;
	}
}