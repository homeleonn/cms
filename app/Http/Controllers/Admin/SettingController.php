<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Post;
use App\Taxonomy;
use Options;

class SettingController
{
	public function actionIndex(){
		global $di;
		$data['settings']['front_page'] = Options::get('front_page');
		$data['settings']['title'] = Options::get('title');
		$data['settings']['description'] = Options::get('description');
		$post = new Post;
		$parent = is_numeric($data['settings']['front_page']) ? $data['settings']['front_page'] : NULL;
		$data['settings']['listForParents'] = $post->listForParents(NULL, $parent);
		
		return view('settings', compact('data'));
	}
	
	public function actionSave(){
		$this->saveMainPageData();
		redirBack();
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