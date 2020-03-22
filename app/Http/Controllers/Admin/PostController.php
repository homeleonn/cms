<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Illuminate\Validation\Rule;
use App\Traits\Helper;
use App\Helpers\{PostsTypes, Arr, Pagination, Transliteration};
use App\Admin\{Post, Media, Test};
use App\{Term, Taxonomy, Relationship, Postmeta};
use DB;
use Options;

class PostController extends Controller
{
	use Helper;
	
	private $routeIndex = 'posts.index';
	
	public function __construct(Request $request)
	{
		if ($request->route() && isset($request->route()->getAction()['as'])) {
			PostsTypes::setCurrentType(explode('.', $request->route()->getAction()['as'])[0]);
			$this->model = new Post;
			$this->postOptions = $this->model->postOptions = PostsTypes::getCurrent();
		}
	}
	
	public function actionIndex()
	{
		$posts = $this->model->list();
		return view('posts.index', compact('posts'));
	}

	
	public function actionCreate()
	{
		$post = $this->model->getCreateData();
		return view('posts.create', compact('post'));
	}
	
	private function setSlug($slugField, $postType, $id = false)
	{
		$slug 			= Transliteration::run($slugField);
		$post 			= Post::wherePost_type($postType);
		$args 			= [$slug, $postType];
		$previousSlug 	= $slug;
		$attempts 		= 10;
		
		if ($id) {
			$args[] = $id;
			$id = ' and id != ?';
		} else {
			$id = '';
		}
		
		while ($attempts-- && selectOne("Select count(*) as count from posts where slug = ? and post_type = ? {$id} limit 1", $args)) {
			$slug = preg_replace_callback('/(\d+)$/', function ($matches) {
				return ++$matches[1];
			}, $slug);
			
			if ($previousSlug === $slug) {
				$slug .= '1';
			}
			
			$args[0] = $slug;
		}
		
		return $slug;
	}
	
	private function inputProcessing($fields, $edit = false, $fieldNameForTranslit = 'title', $type = 'post')
	{
		if (!($fields['title'] ?? null)) {
			$errors[] = 'Заголовок не должен быть пуст';
		}
		
		if ($edit) {
			if (!isset($fields['id'])) {
				RedirBack();
			}
			
			if (!isset($fields['slug'])) {
				RedirBack('Псевдоним не должен быть пуст');
			}
			
			if (($id = $fields['id'] ?? null) && !$postFromDB = Post::find($id)) {
				$errors[] = 'Данной записи не существует';
			}
		}
		
		if (($parent = $fields['parent'] ?? null) && $this->postOptions['hierarchical'] && !Post::find($parent)) {
			$errors[] = 'Данного родителя не существует';
		}
		
		if (isset($errors)) {
			redirBack($errors);
		}
		
		$post = [
			'post_type'		=> $this->postOptions['type'],
			'parent' 		=> $parent,
			'title' 		=> textSanitize($fields['title']),
			'short_title' 	=> textSanitize($fields['short_title'] ?? ''),
			'content' 		=> textSanitize($fields['content'] ?? '', 'content'),
			'slug' 			=> $this->setSlug(
				$fields[$fieldNameForTranslit], 
				$this->postOptions['type'], 
				($edit ? $fields['id'] : null)
			),
			'comment_status' => isset($fields['discussion']) ? 'open' : 'closed',
		];
		
		if ($edit) {
			$post['id'] = $id;
		}
		
		$extraFields = $fields['extra_fileds'] ?? [];
		
		//fill extra fields
		$extraFieldKeys = [];
		$extraFieldKeys = \applyFilter('extra_fields_keys', $extraFieldKeys);
		
		if(!$extraFieldKeys || !is_array($extraFieldKeys)) $extraFieldKeys = [];
		$extraFieldKeys = array_merge($extraFieldKeys, [
			'_jmp_post_template', 
			Options::get('_img'),
		]);
		
		foreach($extraFieldKeys as $key){
			if(isset($fields[$key]) && $fields[$key]){
				$extraFields[$key] = $fields[$key];
				unset($fields[$key]);
			}
		}
		
		return [$post, $extraFields, ($postFromDB ?? null)];
	}

	
	public function actionStore()
	{
		$this->postSave();
		
		return $this->goHome();
	}
	
	private function postSave($edit = false)
	{
		$fields 						= request()->all();
		[$fields, $extraFields, $post] 	= $this->inputProcessing($fields, true, 'slug');
		$receivedTermsIds 				= $this->checkReceivedTerms(request()->get('terms'));
		
		if ($img = request()->get(Options::get('_img')) && !$media = Media::find($img)) {
			redirBack('Ошибка медиа');
		}
		
		DB::beginTransaction();
			doAction('before_post_' . (!$edit ? 'add' : 'edit'), $fields, $extraFields);
			
			if ($edit) {
				$post->fill($fields)->save();
			} else {
				$post = Post::create($fields);
			}
			
			$this->{$edit ? 'updateMeta' : 'insertMeta'}($post->id, $extraFields);
			
			if ($receivedTermsIds) {
				$post->relationship()->sync($receivedTermsIds);
			}
		DB::commit();
		
		return $post;
	}
	
	private function metaFormatting($meta)
	{
		$newMeta = new stdClass();
		
		foreach ($meta as $m) {
			$newMeta->{$m->meta_key} = $m->meta_value;
		}
		
		return $newMeta;
	}
	
	private function updateMeta($postId, $extraFields)
	{
		$postMeta = Postmeta::select('meta_key', 'meta_value')->find($postId);
		
		if ($postMeta) {
			$postMeta = $this->metaFormatting($postMeta);
		}
		
		if (!$extraFields) {
			if ($postMeta) {
				Postmeta::where('post_id', $id)->delete();
			}
		} else {
			$extraFields = Arr::clearHtmlKeysValues($extraFields);
			
			if($postMeta){
				// Обновить существующие, если пришли данные с такими же ключами, но другими значениями
				$existingPostMetaKeys 	= array_keys($postMeta);
				$updateConditions 		= '';
				$placeholderValues 		= $updateKeys = [];
				
				foreach ($extraFields as $key => $value) {
					if(in_array($key, $existingPostMetaKeys)) {
						if ($value != $postMeta[$key]) {
							$updateConditions .= "WHEN id = {$postId} AND meta_key = ? THEN ? ";
							$updateKeys[] = $key;
							array_push($placeholderValues, $key, $value);
						}
						unset($extraFields[$key], $postMeta[$key]);
					}
				}
				
				if ($updateConditions) {
					$updateInKeysPlaceholders 	= Arr::replaceByPlaceholders($updateKeys);
					$placeholderValues 			= array_merge($placeholderValues, $updateKeys);
					
					//Обновляем существующие записи, которые были изменены
					DB::update("Update postmeta SET meta_value = CASE {$updateConditions} END WHERE post_id = {$postId} AND meta_key IN({$updateInKeysPlaceholders})", $placeholderValues);
				}
				
				// Удалить существующие, ключи которых не пришли при редактировании
				if (!empty($postMeta)) {
					Pustmeta::where('post_id', $postId)->whereIn('meta_key', $postMeta)->delete();
				}
			}
			
			// Вставить пришедшие, ключи которых не были найдены в существующих
			$this->insertMeta($extraFields, $postId);
		}
	}
	
	private function checkReceivedTerms($terms)
	{
		if (!$terms) {
			return null;
		}
		
		PostsTypes::checkTaxonomyExists(array_keys($terms), true);
		$receivedTerms 		= $terms;
		$receivedTermsIds 	= Arr::getValuesRecursive($receivedTerms);
		
		$terms = DB::table('terms')->select('id')->whereIn('id', $receivedTermsIds)->get();
		
		if (count($receivedTermsIds) != count($terms)) {
			dd('Ошибка таксономии');
		}
		
		return $receivedTermsIds;
	}
	
	private function insertMeta($meta, $postId, $clear = true)
	{
		if (!empty($meta)) {
			$metaFormatted = [];
			
			foreach ($meta as $key => $value) {
				$metaFormatted[] = [
					'post_id' 		=> $postId,
					'meta_key' 		=> $clear ? $key   : htmlspecialchars($key),
					'meta_value' 	=> $clear ? $value : htmlspecialchars($value),
				];
			}
			
			Postmeta::insert($metaFormatted);
		}
	}

	public function actionEdit($id)
	{
		$post 			= $this->model->getEditData($id);
		$key 			= '_jmp_post_img';
		$post[$key] 	= $this->model->getPostImg($post, $key);
		// $post['comments'] = $this->model->getComments($id);
		$post['__model'] = $this->model;
		return view('posts.edit', compact('post'));
	}

	
	public function actionUpdate(Request $request, $id)
	{
		$this->postSave(true);
		
		return $this->goHome();
	}

	
	public function actionDestroy($id)
	{
		DB::beginTransaction();
			Post::findOrFail((int)$id)->delete();
			Postmeta::wherePost_id($id)->delete();
			Post::whereParent($id)->update(['parent' => 0]);
			DB::update('Update term_taxonomy SET count = count - 1 where count > 0 and term_taxonomy_id IN(Select term_taxonomy_id from term_relationships where object_id = ?)', [$id]);
			Relationship::whereObject_id($id)->delete();
		DB::commit();
		
		return $this->goHome();
	}
	
	public function actionDashboard()
	{
		// DB::beginTransaction();
		// dd(Post::find(217)->fill(['slug' => '3333'])->save(), Post::create(['slug' => '4567']));
		return view('index');
	}
	
	
	
	// TERMS
	
	public function actionTermIndex()
	{
		if (
			!isset($_GET['taxonomy']) || 
			(
				!isset($this->postOptions['taxonomy']) || 
				!array_key_exists($_GET['taxonomy'], $this->postOptions['taxonomy'])
			)
		) abort(404);
		
		return view('terms.index', ['taxonomy' => $_GET['taxonomy'], 'terms' => $this->model->termList($_GET['taxonomy'])]);
	}
	
	public function actionTermCreate()
	{
		if (!isset($_GET['taxonomy'])) {
			return redirect(url()->previous());
		}
		$this->checkGettingTermType($_GET['taxonomy']);
		$data = $this->model->addTermForm($_GET['taxonomy']);
		return view('terms.create', compact('data'));
	}
	
	public function actionTermEdit($id)
	{
		$data = $this->model->editTermForm($id);
		return view('terms.edit', compact('data'));
	}
	
	public function actionTermStore(Request $request)
	{
		$request->merge(textSanitize($request->all()));
		$this->termValidate($request);
		$this->model->addTerm($request->all());
		
		return redirect($this->route('term_index') . '?taxonomy=' . $request->get('taxonomy'));
	}
	
	
	public function actionTermUpdate(Request $request)
	{
		$request->merge(textSanitize($request->all()));
		$this->termValidate($request, true);
		
		$term = Term::find($request->get('id'));
		$term->fill($request->all());
		$term->save();
		
		Taxonomy::where('term_id', $term->id)
					->update([
						'description' 	=> $request->get('description') ?? '',
						'parent' 		=> $request->get('parent')
					]);
		
		return $this->goTerms($request);
	}
	
	private function termValidate(Request $request, $update = null)
	{
		$verifyFields = [
			'taxonomy' 	=> 'required',	
			'name' 		=> 'required',
			'slug' 		=> 'required',
			'parent' 	=> 'required|integer',
		];
		
		if ($update) {
			$verifyFields['id'] = 'required|exists:terms';
		}
		
		$request->validate($verifyFields);
		
		if (!PostsTypes::checkTaxonomyExists($request->get('taxonomy'))) {
			rdr();
		}
	}
	
	public function actionTermDestroy(Request $request, int $id)
	{
		// dd($request->all());
		if ($term = Term::find($id)) {
			$taxonomy = Taxonomy::select('term_taxonomy_id')->where('term_id', $term->id)->first();
			Taxonomy::where('parent', $taxonomy->term_taxonomy_id)->update(['parent' => 0]);
			$taxonomy->delete();
			$term->delete();
		}
		
		return $this->goTerms($request);
	}
	
	private function route($name)
	{
		return route("{$this->postOptions['type']}.{$name}");
	}
	
	private function massRequired(array $fields, Request $request = null)
	{
		$all = $request ? $request->all() : \App::make('request')->all();
		
		foreach ($fields as $field) {
			if (!isset($all[$field])) {
				redir($this->route('term_create') . '?msg=Не все поля заполнены');
			}
		}
		
		return true;
	}
	
	
	
	private function checkGettingTermType($term)
	{
		if(!$this->checkValidTerms($term)){
			$this->goToPostTypePage();
		}
	}
	
	private function checkValidTerms($term)
	{
		return isset($this->postOptions['taxonomy']) ? array_key_exists($term, $this->postOptions['taxonomy']) : false;
	}
	
	private function goToPostTypePage()
	{
		redir(route($this->options['type'] . '.index'));
	}
	
	private function goHome()
	{
		return redirect()->route($this->postOptions['type'] . '.index');
	}
	
	private function goTerms($request)
	{
		return redirect($this->route('term_index') . '?taxonomy=' . $request->get('taxonomy'));
	}
}
