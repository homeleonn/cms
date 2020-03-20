<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postmeta extends Model
{
	protected $table = 'postmeta';
	protected $fillable = ['post_id', 'meta_key', 'meta_value'];
	
    public function posts()
	{
		return $this->hasOne('App\Post');
	}
}
