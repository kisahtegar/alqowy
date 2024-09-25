<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use App\Listeners\AssignStudentRole;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * This class is responsible for registering event listeners for the application.
 * It defines which events are being listened to and their corresponding listeners.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * This property defines the events and their associated listeners.
     * In this case, when the Registered event is triggered, the AssignStudentRole listener 
     * will be invoked.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            AssignStudentRole::class,
        ],
    ];

    /**
     * Bootstrap services.
     *
     * This method is called during the application bootstrapping process. 
     * You can add any additional bootstrapping logic here if needed.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
