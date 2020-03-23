<?php

namespace App\Helpers;

class Msg
{
	public static function success($param = NULL){
		exit('OK' . ($param ? '-'.$param : ''));
	}
	
	public static function jsonCode($code){
		exit(json_encode(array('response' => $code)));
	}
	
	public static function json($data, $code = 1)
	{
		if(!is_array($data)) 		$data = array('msg' => $data);
		if(!isset($data['msg'])) 	$data['msg'] = 'Сообщение';
		if(!isset($data['code'])) 	$data['code'] = $code;
			
		exit(json_encode($data));
	}
	
	public static function set($data){
		exit(json_encode($data));
	}
}