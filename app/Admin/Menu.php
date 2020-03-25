<?php

namespace App\Admin;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
	protected $table 	= 'menu';
    protected $fillable = ['menu_id', 'object_id', 'name', 'origname', 'url', 'type', 'parent', 'sort'];
}
