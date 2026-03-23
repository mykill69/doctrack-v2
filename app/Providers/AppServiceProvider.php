<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Facades\View;
use App\Models\SystemLog;



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

    
    View::composer('home.main', function ($view) {
            $lastLogin = SystemLog::where('user_id', auth()->id())
                ->where('action', 'Login') // ✅ match DB exactly
                ->latest('created_at')
                ->first();

            $view->with('lastLogin', $lastLogin);
        });

          }
}
