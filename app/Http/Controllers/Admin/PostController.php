<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Illuminate\Validation\Rule;
use App\Traits\Helper;
use App\Helpers\{PostsTypes, Arr, Pagination};
use App\Admin\Post;

class PostController extends Controller
{
	use Helper;
	
	private $routeIndex = 'posts.index';
	
	public function __construct(Request $request)
	{
		// dd($request->route()->getAction());
		if ($request->route() && isset($request->route()->getAction()['as'])) {
			PostsTypes::setCurrentType(explode('.', $request->route()->getAction()['as'])[0]);
			$this->model = new Post;
			$this->postOptions = $this->model->postOptions = PostsTypes::getCurrent();
		}
	}
	
	public function actionIndex()
	{
		// dd($this->model->list1());
		$posts = $this->model->list();
		$postOptions = $this->postOptions;
		return view('posts.index', compact('posts', 'postOptions'));
	}

	
	public function actionCreate()
	{
		$post = $this->model->getCreateData();
		
		return view('posts.create', compact('post'));
	}

	
	public function actionStore(Request $request)
	{
		dd($request->all());
		// $post_type = 'page';
		
		$request->validate([
			'title' => 'required'
		]);
		$request->request->add(['post_type' => 'test']);
		// dd($request->input('slug'));
		// dd(\DB::select('Select count(*) as count from posts where slug = ? and post_type = ?', [$slug, '1'])[0]->count);


		// Post::create($request->all());

		return $this->goHome();
	}

	
	public function actionShow($id)
	{
		//
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
			!isset($_GET['term']) || 
			(
				!isset($this->postOptions['taxonomy']) || 
				!in_array($_GET['term'], array_keys($this->postOptions['taxonomy']))
			)
		) abort(404);
		
		return view('terms.index', ['term' => $_GET['term'], 'terms' => $this->model->termList($_GET['term'])]);
	}
	
	public function actionTermEdit($id)
	{
		$data = $this->model->editTermForm($id);
		return view('terms.edit', compact('data'));
	}
	
	public function actionTermCreate()
	{
		$this->checkGettingTermType($_GET['term']);
		$data =  $this->model->addTermForm($_GET['term']);
		return view('terms.create', compact('data'));
	}
	
	public function actionTermStore(Request $request)
	{
		dd($request->all());
	}
	
	
	
	private function checkGettingTermType($term)
	{
		if(!$this->checkValidTerms($term)){
			$this->goToPostTypePage();
		}
	}
	
	private function checkValidTerms($term)
	{
		return isset($this->postOptions['taxonomy']) ? in_array($term, array_keys($this->postOptions['taxonomy'])) : false;
	}
	
	private function goToPostTypePage()
	{
		redir(route($this->options['type'] . '.index'));
	}
	
	
	
	
	
	
	
	
	private function goHome()
	{
		return redirect()->route($this->postOptions['type'] . '.index');
	}
}
