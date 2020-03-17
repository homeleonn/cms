<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
	protected $table = 'term_relationships';
	protected $fillable = ['object_id', 'term_taxonomy_id'];
	public $timestamps = false;
	
	public function post()
	{
		return $this->belongsTo('App\Post', 'id', 'object_id');
	}
	
	public function taxonomy()
	{
		return $this->belongsTo('App\Taxonomy', 'term_taxonomy_id', 'term_taxonomy_id');
	}
}
