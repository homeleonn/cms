<?php

namespace App\Helpers;

class Uploader{
	private $img;
	private $destDir;
	private $rights;
	private $empty;
	
	public function __construct($destDir){
		$this->destDir 	= $destDir;
	}
	
	public function myImgHash(){
		$s = microtime() . rand(0, 999);

		$md5 	= md5($s);
		$base64 = base64_encode($s);

		$md5Start  	 = rand(2,10) + 10;
		$md5End 	 = rand(4,6);

		$md5FirstPart 	  = substr($md5, $md5Start, $md5End);
		$base64FirstPart  = substr($base64, 3, 5);
		$base64SecondPart = substr($base64FirstPart, 1, 2);


		$result = $base64FirstPart . $md5FirstPart . $base64SecondPart;
		
		return str_shuffle($result);
	}
	
	public function getExt($name){
		return pathinfo($name)['extension'];
	}
	
	public function img($src, $name, $rights = 0644){
		$validMime = ['image/gif', 'image/jpeg', 'image/png'];
		
		if($imgData = @getimagesize($src)){
			if(!in_array($imgData['mime'], $validMime))
				return false; //'Недопустимый тип изображения';
			
			$ext = $this->getExt($name);
			do{
				$newName = $this->myImgHash() . '.' . $ext;
				$newSrc  = $this->destDir . $newName;
			}while(is_file($newSrc));
			
			if(!is_dir($dir = dirname($newSrc))){
				mkdir($dir, 0755, true);
			}
			move_uploaded_file($src, $newSrc);
			chmod($newSrc, $rights);
			return [
				'new_src' 	=> $newSrc, 
				'new_name' 	=> $newName, 
				'w' 		=> $imgData[0], 
				'h' 		=> $imgData[1],
				'mime' 		=> $imgData['mime']
			];
		}
		
		return false;
	}
	
	public function thumbCut($src, $mime, $src_w, $src_h, $dest_w = 150, $dest_h = 150){
		if($dest_w > $src_w) $dest_w = $src_w;
		if($dest_h > $src_h) $dest_h = $src_h;
		
		if(($difference = ($src_w - $src_h) / 2) < 0)
			$difference = -$difference;
		
		if($src_w > $src_h){
			$src_w = $src_h;
			$src_x = $difference;
			$src_y = 0;
		}else{
			$src_h = $src_w;
			$src_x = 0;
			$src_y = $difference;
		}
		
		$thumb = imagecreatetruecolor($dest_w, $dest_h);
		$imgType = explode('/', $mime)[1];
		ini_set('gd.jpeg_ignore_warning', true);
		ob_start();
		$source = call_user_func('imagecreatefrom' . $imgType, $src);
		ob_end_clean();
		$alpha = ['png', 'gif'];
		if(in_array($imgType, $alpha)){
			imagealphablending($thumb, false);
			imagesavealpha($thumb, true);
		}
		
		// изменение размера
		imagecopyresized($thumb, $source, 0, 0, $src_x, $src_y, $dest_w, $dest_h, $src_w, $src_h);
		$p = pathinfo($src);
		$thumbName = $p['filename'] . "-{$dest_w}x{$dest_h}." . $p['extension'];
		$thumbSrc = $this->destDir . $thumbName;
		call_user_func_array('image' . $imgType, [$thumb, $thumbSrc]);
		return $thumbName;
	}
}
