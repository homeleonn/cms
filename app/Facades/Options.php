<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Options extends Facade
{
	protected static function getFacadeAccessor()
    {
        return 'Options';
    }
}