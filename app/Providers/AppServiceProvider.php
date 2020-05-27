<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Options;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->singleton('Options', function ($app) {
			return new \App\Helpers\Options(base_path() . '/options.php');
		});
		
		Options::set('controller', '\App\Http\Controllers\PostController');
		// dd(dirname(__DIR__) . '/functions/posttypes.php');
		// dd(file_exists(dirname(__DIR__) . '/functions/posttypes.php'));
		
		require dirname(__DIR__) . '/functions/functions.php';
		
		$this->app['view']->getFinder()->prependLocation(
			resource_path('views/') . Options::get('theme') . '/' . (!isAdminSide() ? 'front' : 'admin')
		);
		
		plugins(Options::get('plugins_activated', true));

		require dirname(__DIR__) . '/functions/posttypes.php';
		require dirname(__DIR__) . '/functions/posttypes1.php';
		
		require public_path() . '/themes/' . Options::get('theme') . '/functions.php';
		
    }
}