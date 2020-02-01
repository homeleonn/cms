<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Category;
use App\Traits\Helper;

class CategoryController extends Controller
{
	use Helper;
	
	private $routeIndex 	= 'categories.index';
	private $viewRoot 		= 'admin.categories';
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		// return $this->view('index', ['categories' => Category::all()]);
		return view('admin.categories.index', ['categories' => Category::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		self::valid($request);
		
		(new Category)
			->fill($request->all())
			->save();
		
		return $this->i();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        echo $id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
		if (!$category = Category::find($id)) {
			return $this->i();
		}
		
        return view('admin.categories.edit', compact('category'));
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
        self::valid($request);
		
		if (!$category = Category::find($id)) {
			return $this->i();
		}
		
		$category->fill($request->all())->save();
		
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
		if (!$category = Category::find($id)) {
			return $this->i();
		}
		
		Category::find($id)->delete();
		
		return $this->i();
    }
	
	private static function valid(Request $request)
	{
		$request->validate([
			'name'	=> 'required'
		]);
	}
}
