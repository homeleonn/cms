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
		PostsTypes::setCurrentType(explode('.', $request->route()->getAction()['as'])[0]);
		$this->model = new Post;
		$this->postOptions = $this->model->postOptions = PostsTypes::getCurrent();
	}
	
	public function actionIndex()
	{
		$posts = $this->model->list();
		return view('admin.posts.index', compact('posts'));
	}

	
	public function actionCreate()
	{
		return view('admin.posts.create');
	}

	
	public function actionStore(Request $request)
	{
		dump($request->all());
		// $post_type = 'page';
		
		$request->validate([
			'title' => 'required'
		]);
		$request->request->add(['post_type' => 'test']);
		// dd($request->input('slug'));
		// dd(\DB::select('Select count(*) as count from posts where slug = ? and post_type = ?', [$slug, '1'])[0]->count);


		Post::create($request->all());

		return $this->goHome();
	}

	
	public function actionShow($id)
	{
		//
	}

	
	public function actionEdit($id)
	{
		$post = Post::find($id);
		return view('admin.posts.edit', compact('post'));
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
	
	public function actionIndex1()
	{
		return view(\Options::get('theme') . '.admin.index');
	}
	
	private function goHome()
	{
		return redirect()->route($this->postOptions['type'] . '.index');
	}
}
