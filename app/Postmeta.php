<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Postmeta extends Model
{
	protected $table = 'postmeta';
	
    public function posts()
	{
		return $this->hasOne('App\Post');
	}
}
