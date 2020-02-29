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
		
	}
	
	public function single($slug, $id = NULL)
	{
		return $id  ? self::find($id) 
					: self::where('slug', $slug)->first();
	}
	
	public function getPostById($id)
	{
		return self::find(6);
		// return DB::table('posts')->($this->select . 'id = ?i', $id);
	}
	
	public function getPostByUrl($url)
	{
		return $this->db->getRow($this->select . 'url = ?s', $url);
	}

    public function getByType($type, $orderBy = false)
	{
		$query = $this->select . 'post_type = ? order by created_at DESC';
		return $this->getAll($query, [$type]);
	}
	
	public function getRawMeta($postId)
	{
		if (!is_array($postId)) {
			$postId = [$postId];
		}
		
		return DB::table('postmeta')
					->select('post_id', 'meta_key', 'meta_value')
					->whereIn('post_id', $postId)
					->get();
	}
	
	public function getMeta($img = false)
	{
		if (!$meta = $this->getRawMeta($this->id)) {
			return $post;
		}
		$postMetaData = [];
		
		foreach ($meta as $m) {
			if ($img && $m->meta_key == '_jmp_post_img') {
				$media = DB::table('media')->find($m->meta_value);
				$m->meta_value = $media->src;
				$this->_jmp_post_img_meta = unserialize($media->meta);
			}
			$this->{$m->meta_key} = $m->meta_value;
			if (strpos($m->meta_key, '_') === 0) continue;
			$postMetaData[$m->meta_key] = $m->meta_value;
		}
		
		$this->meta_data = $postMetaData;
	}
	
	public function getPostTerms($postId, $taxonomyKeys, $where = null)
	{
		// if (!$where) {
			// return false;
		// }
		// private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	
		// ' and tr.object_id = ' . $post['id'] . ' and tt.taxonomy IN(\''.implode("','", $this->postOptions['taxonomy']).'\')'
		
		// return $this->db->getAll('Select DISTINCT t.*, tt.* from ' . str_replace(['posts p,', 'and p.id = tr.object_id'], '', $this->relationship) . $where);
		
		return DB::table('terms as t')
					->distinct()
					->join('term_taxonomy as tt', 't.id', '=', 'tt.term_id')
					->join('term_relationships as tr', 	'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
					->select('t.*', 'tt.*')
					->where('tr.object_id', $postId)
					->whereIn('tt.taxonomy', $taxonomyKeys)
					->get();
	}
	
	
	private function getAll($query, $params, $array = false, $count = null)
	{//dd(func_get_args());
		if (!is_array($params) || $array) $params = [$params];
		// dd(DB::select('Select * from posts limit 1'));
		// $this->checkInLimit($query, $params, $count);
		// $data = call_user_func_array(['DB', 'Select'], array_merge([$query . $this->limit], $params));
		// dd(array_merge([$query], [$params]));
		// dd([$query], $query, $params);
		return forward_static_call_array (['DB', 'Select'], array_merge([$query], [$params]));
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
