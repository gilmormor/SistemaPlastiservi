<?php

namespace App\Providers;

use App\Models\Admin\Menu;
use App\Models\Dte;
use App\Models\DteFac;
use App\Observers\DteFacObserver;
use App\Observers\DteObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        //Dte::observe(DteObserver::class);
        //DteFac::observe(DteFacObserver::class);
        View::composer("theme.lte.aside", function ($view) {
            $menus = Menu::getMenu(true);
            $view->with('menusComposer', $menus);
        });
        View::share('theme','lte');
    }
}
