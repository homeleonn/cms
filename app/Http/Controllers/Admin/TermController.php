<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Term;

class TermController extends Controller
{
	private $type;
	private $builder;
	private $hierarchical;
	
	public function __construct()
	{
		if (isset(\Route::current()->action['as'])) {
			$this->type 		= explode('.', \Route::current()->action['as'])[0];
			$this->builder 		= Term::where('taxonomy', $this->type);
			$this->hierarchical = $this->type == 'tag' ? false : true;
		}
	}
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$terms 			= $this->builder->get();
		$termsHierarchy = $this->hierarchical ? $this->hierarchyItems($terms) : $terms;
		
        dd($terms, $termsHierarchy);
    }
	
	public function hierarchyItems($items, $selfId = NULL, $parent = NULL, $addKeys = [])
	{
		// dd($items, empty($items), !count($items));
		if(empty($items) || !count($items)){
			return [];
		}
		
		// $isTerm = isset($items[0]->taxonomy);
		
		foreach ($items as $item) {
			if ($item->id == $selfId) {
				continue;
			}
			
			if (!empty($addKeys)) {
				foreach ($addKeys as $key => $values) {
					if (!$values) {
						break;
					}
					
					if (isset($addKeys[$key][$item->id])) {
						$item[$key] = $addKeys[$key][$item->id];
						unset($addKeys[$key][$item->id]);
					} else {
						$item[$key] = [];
					}
				}
			}
			
			$itemsToParents[$item->parent ?? 0][] = $item;
		}
		
		if (!isset($itemsToParents)) {
			return [];
		}
		
		ksort($itemsToParents);
		$itemsToParents = array_reverse($itemsToParents, true);
		
		if ($this->hierarchical) {
			foreach ($itemsToParents as &$items) {
				foreach ($items as &$item) {
					if (isset($itemsToParents[$item->id])) {
						$item->children = $itemsToParents[$item->id];
						unset($itemsToParents[$item->id]);
					}
				}
			}
		}
		
		// dd($itemsToParents);
		
		return $itemsToParents[0];
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
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
