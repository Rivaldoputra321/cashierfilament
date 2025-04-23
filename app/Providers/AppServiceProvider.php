<?php

namespace App\Providers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;

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
        FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): View => view('filament.pages.auth.login_extra')
        );
        Gate::define('view_transaction_page', function ($user) {
            return $user->role === 'employee'; // Sesuaikan dengan logic role kamu
        });
    }
}
