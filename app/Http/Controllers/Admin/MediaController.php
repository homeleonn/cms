<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Admin\Media;
use App\Helpers\Uploader;
use App\Helpers\Msg;
use DB;

class MediaController extends Controller
{
	public function actionIndex($async = false)
	{
		// dd($async);
		$data['media'] = Media::orderBy('id', 'desc')->get();
		$data['title'] = 'Медиа';
		$data['async'] = $async;
		
		return view('media.index', compact('data'));
		
		if($async === 'async'){
			$this->view->render('media/show', $data, false);
		}else{
			return $data;
		}
	}
	
	public function actionAdd(){//echo(json_encode($_FILES));exit;
		if(!isset($_FILES['files'])) return 0;
		$files = $_FILES['files'];
		$upDir = date('Y/m', time()) . '/';
		$dir = uploads_path() . $upDir;
		$urlDir = uploads_url() . $upDir;
		$uploader = new Uploader($dir);
		$thumbW = 150;
		$thumbH = 150;
		$mediumW = 320;
		$mediumH = 320;
		$thumbSrcList = [];
		$insert = [];
		$i = 0;
			
		// Игнорим возникновение ошибок при ломаных изображениях
		ini_set('gd.jpeg_ignore_warning', 1);
		
		while(isset($files['name'][$i])){
			if($files['size'][$i] > 2 * 1024 * 1024){
				Msg::json(['error' => 'Изображение слишком большое, попробуйте сначала уменьшить размер']);
			}
			
			if(($result = $uploader->img($files['tmp_name'][$i], $files['name'][$i])) !== false){
				$src = $upDir . $result['new_name'];
				$thumbSrcList[$i]['orig'] = $urlDir . $result['new_name'];
				
				$meta = [
					'width' => $result['w'],
					'height' => $result['h'],
					'dir' => $upDir
				];
				
				
				// Создаем миниатюру если ширина или высота больше предполагаемых размеров миниатюры
				if($result['w'] > $thumbW || $result['h'] > $thumbH){
					$thumbSrcList[$i]['thumb'] = $urlDir . $uploader->thumbCut($result['new_src'], $result['mime'], $result['w'], $result['h'], $thumbW, $thumbH);
					
					$meta['sizes']['thumbnail'] = [
						'file' => self::addPrefix($result['new_name'], "-{$thumbW}x{$thumbH}"),
						'width' => $thumbW,
						'height' => $thumbH,
						'mime' => $result['mime'],
					];
				}
				
				// Ресайзим до средней ширины если ширина или высота больше предполагаемых размеров миниатюры
				if($result['w'] > $mediumW || $result['h'] > $mediumH){
					list($mediumName, $width, $height) = $this->resize($dir, $result['new_name'], $mediumW, $mediumH);
					//$thumbSrcList[$i]['medium'] = $urlDir . $mediumName;
					
					$meta['sizes']['medium'] = [
						'file' => $mediumName,
						'width' => $width,
						'height' => $height,
						'mime' => $result['mime'],
					];
				}
				
				$metaForMediaShow = $meta;
				unset($metaForMediaShow['sizes']['thumbnail']);
				$thumbSrcList[$i]['meta'] = json_encode($metaForMediaShow);
				$thumbSrcList[$i]['dir'] = uploads_url() . $meta['dir'];
				
				$meta = serialize($meta);
				
				$insert[] = [$src, $files['name'][$i], $files['type'][$i], $meta];
			}
			
			$i++;
			
			if($i > 50) break;
		}
		
		if (!empty($insert)) {
			$i = 0;
			
			DB::raw('LOCK TABLE media WRITE');
				DB::beginTransaction();
					
					foreach($insert as $ins){
						DB::insert('INSERT INTO media (src, name, mime, meta) VALUES (?,?,?,?)', $ins);
						$thumbSrcList[$i++]['id'] = DB::getPdo()->lastInsertId();
					}
					
				DB::commit();
			DB::raw('UNLOCK TABLES');
			
			Msg::json(['thumbSrcList' => $thumbSrcList]);
		}
	}
	
	private function resize($destDir, $source, $destW, $destH, $quality = 70){
		$fullPath = $destDir . $source;
		$sizes = getimagesize($fullPath);
		if($destW > $sizes[0]) $destW = $sizes[0]; 
		if($destH > $sizes[1]) $destH = $sizes[1];
		
		if($sizes[0] >= $sizes[1]){
			$ratio = $destW / $sizes[0];
			$newW = $destW;
			$newH = (int)round($sizes[1] * $ratio);
		}else{
			$ratio = $destH / $sizes[1];
			$newW = (int)round($sizes[0] * $ratio);
			$newH = $destH;
		}
		
		$destName = self::addPrefix($source, "-{$newW}x{$newH}");
		
		$image_p = imagecreatetruecolor($newW, $newH);
		$imgType = explode('/', $sizes['mime'])[1];
		
		ob_start();
		$image = call_user_func('imagecreatefrom' . $imgType, $fullPath);
		ob_end_clean();
		
		$imageParams = [$image_p, $destDir . $destName];
		
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newW, $newH, $sizes[0], $sizes[1]);
		
		$alpha = ['png', 'gif'];
		if(in_array($imgType, $alpha)){
			imagealphablending($image_p, false);
			imagesavealpha($image_p, true);
		}else{
			$imageParams[] = $quality; // quality
		}
		
		call_user_func_array('image' . $imgType, $imageParams);
		
		return [$destName, $newW, $newH];
	}
	
	public function actionDel($id)
	{
		$media = selectRow('Select * from media where id = ?', [(int)$id]);
		
		if($media){
			$src = uploads_path() . $media['src'];
			$meta = unserialize($media['meta']);
			
			$delMedia = [$src];
			$dir = pathinfo($src)['dirname'] . '/';
			if(isset($meta['sizes']['thumbnail'])) 	$delMedia[] = $dir . $meta['sizes']['thumbnail']['file'];
			if(isset($meta['sizes']['medium'])) 	$delMedia[] = $dir . $meta['sizes']['medium']['file'];
			
			array_map('unlink', $delMedia);
			DB::delete('Delete from media where id = ?', [$media['id']]);
			DB::delete('Delete from postmeta where meta_key = ? and meta_value = ?', ['_jmp_post_img', $media['id']]);
		}
		
		exit;
	}
	
	public static function addPrefix($string, $prefix, $delim = '.')
	{
		return str_replace($delim, $prefix . $delim, $string);
	}
}