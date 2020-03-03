<?php

namespace App;

use DB;
use App\Helpers\{PostsTypes, Arr};

class Taxonomy
{
	private $select = 'Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and ';
	private $postTypeTaxonomies;
	private static $cache;
	
	public function getAll($where, $args)
	{
		static $cache;
		if (!isset($cache[$where])) {
			$cache[$where] = DB::select($this->select . $where, $args);
		}
		return $cache[$where];
	}
	
	public function getAllByPostTypes($postType)
	{
		if (!$this->postTypeTaxonomies) {
			$this->postTypeTaxonomies = DB::select('Select DISTINCT t.*, tt.* from posts as p, terms as t, term_taxonomy as tt, term_relationships as tr where t.id = tt.term_id and tt.term_taxonomy_id = tr.term_taxonomy_id and p.id = tr.object_id and p.post_type = \''.$postType.'\'');
		}
		
		return $this->postTypeTaxonomies;
	}
	
	
	/**
	 *  @brief Brief description
	 *  
	 *  @param [in] $terms Description for $terms
	 *  @param [in] $by Description for $by
	 *  @param [in] $value Description for $value
	 *  @param [in] $onlyOne Description for $onlyOne
	 *  @return Return description
	 *  
	 *  @details More details
	 */
	public function filter(array $terms, string $by, string $value, $onlyOne = false)
	{
		$result = false;
		foreach ($terms as $term) {
			if (!isset($term->$by)) return $result;
			if ($term->$by == $value) {
				if($onlyOne)
					return $term;
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
	
	public function getAllByObjectsIds($objectsIds)
	{
		return DB::table('terms as t')
					->join('term_taxonomy as tt', 		't.id', '=', 'tt.term_id')
					->join('term_relationships as tr', 	'tt.term_taxonomy_id', '=', 'tr.term_taxonomy_id')
					->select('t.*', 'tt.*', 'tr.object_id')
					->whereIn('tr.object_id', $objectsIds)
					->get()->toArray();
	}
	
	public function getByTaxonomies($taxonomies = NULL)
	{
		$taxonomies = $taxonomies ?: (PostsTypes::get('taxonomy') ? array_keys(PostsTypes::get('taxonomy')) : null);
		if (empty($taxonomies)) return [];
		if (($cache = self::cache($taxonomies)) === NULL) {
			self::cache($taxonomies, DB::select('Select t.*, tt.* from terms as t, term_taxonomy as tt where t.id = tt.term_id and tt.taxonomy IN('.Arr::getCountItemsLikeQuestionsMark($taxonomies).')', $taxonomies));
		}
		
		return self::cache($taxonomies);
	}
	
	public static function cache($key, $value = NULL)
	{
		if (is_array($key)) $key = implode(',', $key);
		if ($value === NULL) {
			return isset(self::$cache[$key]) ? self::$cache[$key] : NULL;
		} else {
			self::$cache[$key] = $value;
		}
	}
}