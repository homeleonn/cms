<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
	protected $fillable = ['name', 'slug'];
	protected $table = 'terms1';
	public $timestamps = false;
	
	public function taxonomy()
	{
		return $this->belongsTo('App\Taxonomy', 'term_id', 'id');
	}
}
