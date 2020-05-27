<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
		
		// 'auth.login' => [
			// 'App\Events\AuthLoginEventHandler',
		// ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
				
		// Event::listen('Illuminate\Database\Events\StatementPrepared', function ($event) {
            // $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        // });
		
		// Возникает при успешных входах...
		// Event::listen('auth.login', function ($user, $remember) {
			// dd(1);
		// });
		
		Event::listen('Illuminate\Auth\Events\Login', function ($event) {
			session(['user.accesslevel' => $event->user->accesslevel]);
		});
		
		Event::listen('Illuminate\Auth\Events\Logout', function ($event) {
			session()->forget('user');
		});
		
    }
}
