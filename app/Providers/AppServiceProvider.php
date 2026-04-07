<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\PesanKontak;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        View::composer('user.layout', function ($view) {
            $inboxCount = 0;
            if (Auth::check()) {
                $inboxCount = PesanKontak::where('id_user', Auth::id())
                    ->whereNotNull('balasan_admin')
                    ->where(function ($q) {
                        $q->whereNull('dibaca_user')->orWhere('dibaca_user', false);
                    })
                    ->count();
            }
            $view->with('inboxNotifCount', $inboxCount);
        });
    }
}
