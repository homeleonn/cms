<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Helpers\{Arr, Options};

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'short_title', 'content', 'post_type', 'parent', 'author', 'status', 'comment_status'];
	private $relationship = 'posts p, terms t, term_taxonomy tt, term_relationships tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id ';
	private $allItemsCount;
	private $limit;
	private $offset;
	public $taxonomy;
	public $postOptions;

	public function __construct(...$args)
	{
		parent::__construct(...$args);
		$this->taxonomy = new Taxonomy;
	}

	public function single($slug, $id = NULL)
	{
		return $id  ? self::find($id) 
					: self::where('slug', $slug)->first();
	}

    public function getByType($type, $orderBy = false)
	{
		$query = 'Select * from `posts` where post_type = ? order by created_at DESC';
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
			return;
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
	{
		if (!is_array($params) || $array) $params = [$params];
		$this->checkInLimit($query, $params, $count);
		return forward_static_call_array (['DB', 'Select'], array_merge([$query . $this->getLimit()], [$params]));
	}
	
	
	private function checkInLimit($query, $params, $count = null)
	{
		$from 	= ['Select *', 'Select distinct p.*'];
		$to 	= ['Select COUNT(*) as count', 'Select COUNT(p.id) as count'];
		$this->allItemsCount = selectOne(str_replace($from, $to, $query), $params);
		
		if (!$this->allItemsCount || $this->allItemsCount <= $this->getOffset()) {
			redir(preg_replace('~page/\d+/?~', '', url()->current()));
		}
	}
	
	public function getAllItemsCount()
	{
		return $this->allItemsCount;
	}
	
	public function getOffset()
	{
		return $this->offset;
	}
	
	public function setOffset($page, $perPage)
	{
		$this->limit 	= $perPage;
		$this->offset 	= !$perPage ? 0 : ($page - 1) * $perPage;
	}
	
	public function getLimit()
	{
		return ' LIMIT ' . $this->limit . ' OFFSET ' . $this->getOffset();
	}
	
	public function getPostsBysTermsTaxonomyIds($termsTaxonomyIds, $orderBy = false)
	{
		$orderBy = $orderBy ? implode(', p.', $orderBy[0]) . ' ' . $orderBy[1] : 'p.created_at DESC';
		$query = 'Select distinct p.* from ' . $this->relationship . 'and tt.term_taxonomy_id IN('.Arr::getCountItemsLikeQuestionsMark($termsTaxonomyIds).') group by p.id order by ' . $orderBy;
		return $this->getAll($query, $termsTaxonomyIds);
	}
	
	public function getTheme()
	{
		return Options::get('theme');
	}
	
	public function metas()
	{
		return $this->hasMany('App\Postmeta', 'post_id', 'id');
	}
}
