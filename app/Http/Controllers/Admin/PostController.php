<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Illuminate\Validation\Rule;
use App\Traits\Helper;
use App\Helpers\{PostsTypes, Arr, Pagination, Transliteration};
use App\Admin\{Post, Media, Test};
use App\{Term, Taxonomy, Relationship};
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
		$slug = Transliteration::run($slugField);
		$post = Post::wherePost_type($postType);
		
		$args = [$slug, $postType];
		if ($id) {
			$id = ' and id != ?';
			$args[] = $id;
		} else {
			$id = '';
		}
		
		$previousSlug = $slug;
		$attempts = 10;
		
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
			$errors[] = 'Заголовок не должен быть пуст!';
		}
		
		if ($edit) {
			if (!isset($fields['id'])) {
				RedirtBack();
			}
			
			if (($id = $fields['id'] ?? null) && Post::find($id)) {
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
			'slug' 			=> $this->setSlug($fields['title'], $this->postOptions['type'], $edit ? $fields['id'] : null),
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
		
		return [$post, $extraFields];
	}

	
	public function actionStore(Request $request)
	{
		$fields = request()->all();
		[$fields, $extraFields] = $this->inputProcessing($fields);
		
		if ($terms = request()->get('terms')) {
			PostsTypes::checkTaxonomyExists(array_keys($terms), true);
			$receivedTerms 		= $terms;
			$receivedTermsIds 	= Arr::getValuesRecursive($receivedTerms);
			
			$terms = DB::table('terms')->select('id')->whereIn('id', $receivedTermsIds)->get();
			
			if (count($receivedTermsIds) != count($terms)) {
				dd('Ошибка таксономии');
			}
		}
		
		if ($img = $request->get(Options::get('_img')) && !$media = Media::find($img)) {
			redirBack('Ошибка медиа');
		}
		
		DB::beginTransaction();
			$post = Post::create($fields);
			
			doAction('after_post_add', $fields);
			$fields['id'] = $post->id;
			
			$this->insertMeta($post->id, $extraFields);
			
			if ($terms) {
				$post->relationship()->attach($receivedTermsIds);
			}
		DB::commit();
		
		return $this->goHome();
	}
	
	private function termsSync($postId, $termsIds)
	{
		
	}
	
	private function insertMeta($postId, $fields)
	{
		$meta = [];
		if (!empty($fields)) {
			foreach ($fields as $key => $value) {
				$meta[] = [
					'post_id' 		=> $postId,
					'meta_key' 		=> htmlspecialchars($key),
					'meta_value' 	=> htmlspecialchars($value),
				];
			}
		}
		
		if($meta) {
			DB::table('postmeta')->insert($meta);
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
		$post = Post::find($id);
		// dump($request->all());
		$this->validate($request, [
		    'url' => [
		        Rule::unique('posts')->ignore($post->id),
		    ],
		    'post_type' => [
		        Rule::unique('posts')->ignore($post->id),
		    ],
		]);

		$post->fill($request->all())->save();
		
		return $this->goHome();
	}

	
	public function actionDestroy($id)
	{
		//
	}
	
	public function actionDashboard()
	{
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
