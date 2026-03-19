<?php

namespace App\Providers;

use App\Models\Admin\Menu;
use App\Models\CotizacionDetalle;
use App\Models\Dte;
use App\Models\DteDet;
use App\Models\DteFac;
use App\Models\InvMovDet;
use App\Models\NotaVentaDetalle;
use App\Observers\DteObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Observers\CotizacionDetalleObserver;
use App\Observers\NotaVentaDetalleObserver;
use App\Observers\DteDetObserver;
use App\Observers\InvMovDetObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        CotizacionDetalle::observe(CotizacionDetalleObserver::class);
        NotaVentaDetalle::observe(NotaVentaDetalleObserver::class);
        DteDet::observe(DteDetObserver::class);

        // Registra el nuevo observer para invmovdet
        InvMovDet::observe(InvMovDetObserver::class);

        View::composer("theme.lte.aside", function ($view) {
            $menus = Menu::getMenu(true);
            $view->with('menusComposer', $menus);
        });
        View::share('theme','lte');
    }
}
