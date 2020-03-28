<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;
use App\Helpers\{PostsTypes, Arr};

class Taxonomy extends Model
{
	protected $primaryKey = 'term_taxonomy_id';
	protected $fillable = ['term_id', 'taxonomy', 'description', 'parent', 'count'];
	protected $table = 'term_taxonomy';
	public $timestamps = false;
	private $postTypeTaxonomies;
	private static $cache;
	
	public function terms()
	{
		return $this->hasMany('App\Term', 'id', 'term_id');
	}
	
	public function relationship()
	{
		return $this->hasMany('App\Relationship', 'term_taxonomy_id', 'term_taxonomy_id');
	}
	
	public function getAll($where, $args)
	{
		static $cache;
		
		if (!isset($cache[$where])) {
			$cache[$where] = DB::select('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and ' . $where, $args);
		}
		
		return $cache[$where];
	}
	
	
	/**
	 *  Save terms by given taxonomy
	 *  
	 *  @param $terms list of terms
	 *  @param $by term field which need find
	 *  @param $taxonomy taxonomy name
	 *  @param bool $onlyOne
	 *  
	 *  @return filtered terms by taxonomy
	 */
	public function filter(array $terms, string $by, string $taxonomy, $onlyOne = false)
	{
		$result = false;
		
		foreach ($terms as $term) {
			if (!isset($term->$by)) {
				return $result;
			}
			
			if ($term->$by == $taxonomy) {
				if ($onlyOne) {
					return $term;
				}
				
				$result[] = $term;
			}
		}
		
		return $result;
	}
	
	public function whatIs($terms, $name)
	{
		foreach ($terms as $term) {
			if ($term['name'] == $name) {
				return $term['taxonomy'];
			}
		}
		
		return false;
	}
	
	public function getAllByObjectsIds($objectsIds, $added = null)
	{
		return $this->relation()
					->whereIn('tr.object_id', $objectsIds)
					->get()->toArray();
	}
	
	public function relation()
	{
		return DB::table('terms as t')
					->join('term_taxonomy as tt', 		't.id', '=', 'tt.term_id')
					->join('term_relationships as tr', 	'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
					->select('t.*', 'tt.*', 'tr.object_id');
	}
	
	public function getByTaxonomies($taxonomies = NULL)
	{
		$taxonomies = $taxonomies ?: (PostsTypes::get('taxonomy') ? array_keys(PostsTypes::get('taxonomy')) : null);
		
		if (empty($taxonomies)) {
			return [];
		}
		
		if (($cache = self::cache($taxonomies)) === NULL) {
			self::cache($taxonomies, DB::select('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and tt.taxonomy IN('.Arr::getCountItemsLikeQuestionsMark($taxonomies).')', $taxonomies));
		}
		
		return self::cache($taxonomies);
	}
	
	public static function cache($key, $value = NULL)
	{
		if (is_array($key)) {
			$key = implode(',', $key);
		}
		
		if ($value === NULL) {
			return isset(self::$cache[$key]) ? self::$cache[$key] : NULL;
		} else {
			self::$cache[$key] = $value;
		}
	}
}