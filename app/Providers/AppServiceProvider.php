<?php

namespace App\Providers;

use App\Models\Karyawan;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        // @can/@canany memakai izin_akses role karyawan (super admin lolos semua)
        Gate::before(function ($user, string $ability) {
            $user = $user instanceof Karyawan ? $user : auth('karyawan')->user();

            if ($user instanceof Karyawan) {
                return $user->hasPermission($ability) ? true : null;
            }

            return null;
        });
    }
}
