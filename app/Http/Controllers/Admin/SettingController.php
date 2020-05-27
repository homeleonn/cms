<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Admin\Post;
use App\Taxonomy;
use Options;

class SettingController extends Controller
{
	public function actionIndex()
	{
		$data['settings']['front_page']  	= Options::get('front_page');
		$data['settings']['title'] 			= Options::get('title');
		$data['settings']['description'] 	= Options::get('description');
		$post 								= new Post;
		$parent 							= is_numeric($data['settings']['front_page']) ? $data['settings']['front_page'] : NULL;
		$data['settings']['listForParents'] = $post->listForParents(NULL, $parent);
		
		return view('settings', compact('data'));
	}
	
	public function actionSave(){
		$this->saveMainPageData();
		redirBack();
	}
	
	public function actionTypes()
	{
		// dd(Options::get('posttypes', true));
		// return Options::get('posttypes');
		return view('settings.posttypes', ['posttypes' => Options::get('posttypes', true)]);
	}
	
	public function actionTypesSave()
	{
		if (!$types = request()->get('posttypes')) {
			redirBack();
		}
		
		$types = $this->checkPosttypesUniquals($types);
		
		Options::save('posttypes', $types, true);
		
		redirBack('messages', 'Действие выполнено успешно!');
	}
	
	private function checkPosttypesUniquals($types)
	{
		$typesDuplicate = $types;
		$newTypes = [];
		
		foreach ($types as $key => &$type) {
			if (!isset($type['type'])) {
				redirBack();
			}
			
			if (!isset($type['archive']) || is_null($type['archive'])) {
				$type['archive'] = '';
			}
			
			$type['hierarchical'] = isset($type['hierarchical']) ? 1 : 0;
			
			foreach ($typesDuplicate as $key2 => $type2) {
				if ($key == $key2) {
					continue;
				}
				
				if ($type['type'] == $type2['type'] || ($type['archive'] && $type2['archive'] && $type['archive'] == $type2['archive'])) {
					redirBack('Типы и архивы должны быть уникальны');
				}
			}
			
			$newTypes[$type['type']] = $type;
			unset($typesDuplicate[$key]);
		}
		
		return $newTypes;
	}
	
	private function saveMainPageData(){
		$input = request()->all();
		
		if(isset($input['front_page'])){
			$save = false;
			switch($input['front_page']){
				case 'last':
					$save = 'last';
				break;
				case 'static':
					if(isset($input['parent']) && is_numeric($input['parent']) && $input['parent']){
						$save = $input['parent'];
					}
				break;
			}
			
			if($save){
				Options::save('front_page', $save); 
				clearCache('pages/' . $save);
			}
		}
		
		if(isset($input['title'])){
			Options::save('title', textSanitize($input['title'])); 
		}
		
		if(isset($input['description'])){
			Options::save('description', textSanitize($input['description'])); 
		}
	}
	
	
	public function actionCacheClear()
	{
		if ($objs = glob(uploads_path() . "cache/*")) {
			foreach ($objs as $obj) {
				if (is_dir($obj)) {
					do_rmdir($obj);
				}
			}
		}
		
		exit;
	}
}