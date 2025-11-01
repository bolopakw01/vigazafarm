<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Kandang;

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
        // Share list of kandang to admin views so forms can access them
        try {
            $kandangs = Kandang::orderBy('nama_kandang')->get();
        } catch (\Exception $e) {
            // In case of migration/DB not ready, provide empty collection to avoid breaking views
            $kandangs = collect();
        }

        // Share only to admin views (prefix 'admin.') and partials
        View::composer(['admin.*', 'admin.*.*'], function ($view) use ($kandangs) {
            $view->with('kandangs', $kandangs);
        });
    }
}
