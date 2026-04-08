<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\PesanKontak;
use App\Models\ChatMessage;

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
                $inboxCount = ChatMessage::where('id_user', Auth::id())
                    ->where('pengirim', 'admin')
                    ->where('dibaca', false)
                    ->count();
            }
            $view->with('inboxNotifCount', $inboxCount);
        });

        View::composer('admin.layout', function ($view) {
            $chatNotifCount = ChatMessage::where('pengirim', 'user')
                ->where('dibaca', false)
                ->distinct('id_user')
                ->count('id_user');
            $view->with('adminChatNotifCount', $chatNotifCount);
        });
    }
}
