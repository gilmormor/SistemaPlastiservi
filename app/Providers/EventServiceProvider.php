<?php

namespace App\Providers;

use App\Events\FinSesionUsuario;
use App\Events\InicioSesionUsuario;
use App\Listeners\BitFinSesionUsuario;
use App\Listeners\BitInicioSesionUsuario;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    /*
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    */
    protected $listen = [
        InicioSesionUsuario::class => [
            BitInicioSesionUsuario::class,
        ],
        FinSesionUsuario::class => [
            BitFinSesionUsuario::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
