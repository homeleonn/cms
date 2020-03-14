<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
	public function taxonomy()
	{
		return $this->belongsTo('App\Taxonomy', 'term_id', 'id');
	}
}
