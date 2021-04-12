<?php

namespace App\Providers;

use App\Events\CerrarSolDesp;
use App\Events\DevolverSolDesp;
use App\Events\FinSesionUsuario;
use App\Events\GuardarFacturaDespacho;
use App\Events\GuardarGuiaDespacho;
use App\Events\InicioSesionUsuario;
use App\Events\Notificacion;
use App\Listeners\BitFinSesionUsuario;
use App\Listeners\BitInicioSesionUsuario;
use App\Listeners\CerrarSolDespNotificacion;
use App\Listeners\CrearNotificacion;
use App\Listeners\DevolverSolDespNotificacion;
use App\Listeners\NotifyMailGuardarFacturaDespacho;
use App\Listeners\NotifyMailGuardarGuiaDespacho;
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
        ],
        GuardarGuiaDespacho::class => [
            NotifyMailGuardarGuiaDespacho::class,
        ],
        GuardarFacturaDespacho::class => [
            NotifyMailGuardarFacturaDespacho::class,
        ],
        DevolverSolDesp::class => [
            DevolverSolDespNotificacion::class,
        ],
        CerrarSolDesp::class => [
            CerrarSolDespNotificacion::class,
        ],
        Notificacion::class => [
            CrearNotificacion::class,
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
