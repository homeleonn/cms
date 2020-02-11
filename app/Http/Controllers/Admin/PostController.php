<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Post;
use Validator;
use Illuminate\Validation\Rule;
use App\Traits\Helper;

class PostController extends Controller
{
	use Helper;
	
	private $routeIndex = 'posts.index';
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$posts = Post::all();
		return view('admin.posts.index', compact('posts'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		return view('admin.posts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$messages = [
		    'data.ip.unique' => 'Given url and post type are not unique',
		];

		// dd($request->input('slug'));


		$request->request->add(['slug.post_type' => $request->input('slug') . '.page']);
		dump($request->all());
		$slug = $request->input('slug');
		$post_type = 'page';
		dd(\DB::select('Select count(*) as count from posts where slug = ? and post_type = ?', [$slug, '1'])[0]->count);
		// Validator::make($request->all(), 
		// 	[
		// 		'name' => 'unique:games,name,NULL,id,user_id,'.$user->id
		// 		'slug' => 'unique:posts,slug,NULL,post_type,post_type,'.$user->id
		// 	    'slug.post_type' => [
		// 	        'required',
		// 	        Rule::unique('posts')->where(function ($query) use ($slug, $post_type) {
		// 	            return $query->where('slug', $slug)
		// 	            ->where('post_type', $post_type);
		// 	        }),
		// 	    ],
		// 	],
		// 	$messages
		// );
		// Post::create($request->all());

		// return $this->i();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		$post = Post::find($id);
		return view('admin.posts.edit', compact('post'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
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
		
		return $this->i();
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
