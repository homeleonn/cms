<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use App\Helpers\Arr;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'short_title', 'content', 'post_type', 'parent', 'author', 'status', 'comment_status'];
	public $select = 'Select * from posts where ';
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	private $allItemsCount;
	private $start = 1;
	
	public function __construct()
	{
		$this->taxonomy = new Taxonomy();
	}

    public function getByType($type, $orderBy = false)
	{
		$query = $this->select . 'post_type = ? order by created_at DESC';
		return $this->getAll($query, [$type]);
	}
	
	
	private function getAll($query, $params, $array = false, $count = null)
	{//dd(func_get_args());
		// if($array) $params = [$params];
		// dd(DB::select('Select * from posts limit 1'));
		// $this->checkInLimit($query, $params, $count);
		// $data = call_user_func_array(['DB', 'Select'], array_merge([$query . $this->limit], $params));
		// dd(array_merge([$query], $params));
		return call_user_func_array(['DB', 'Select'], array_merge([$query], $params));
	}
	
	
	private function checkInLimit($query, $params, $count)
	{
		$this->allItemsCount = $count ?: (int)call_user_func_array(['DB', 'Select'], array_merge([str_replace('Select *', 'Select COUNT(*) as count', $query)], $params));
		
		if ($this->allItemsCount && $this->allItemsCount <= $this->start) {
			dump(preg_replace('~page/\d+/?~', '', FULL_URL));
			// $this->request->location(preg_replace('~page/\d+/?~', '', FULL_URL));
		}
	}
	
	public function getPostsBysTermsTaxonomyIds($termsTaxonomyIds, $orderBy = false)
	{
		$orderBy = $orderBy ? implode(', p.', $orderBy[0]) . ' ' . $orderBy[1] : 'p.created_at DESC';
		$query = 'Select distinct p.* from ' . $this->relationship . 'and tt.term_taxonomy_id IN('.Arr::getCountItemsLikeQuestionsMark($termsTaxonomyIds).') group by p.id order by ' . $orderBy;
		$countCache = DB::select(str_replace('distinct p.*', 'count(distinct p.id) as count', $query), $termsTaxonomyIds);
		$count = 0;
		if($countCache){
			foreach($countCache as $c)
				$count += $c->count;
		}
		return $this->getAll($query, [$termsTaxonomyIds], true, $count);
	}
}
