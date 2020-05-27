<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

/**
 * @method static where(string $string, $email)
 */
class User extends Model implements Authenticatable
{
	use AuthenticableTrait;
	
    protected $fillable = ['id', 'name', 'email', 'password', 'remember_token', 'created_at', 'updated_at'];
}
