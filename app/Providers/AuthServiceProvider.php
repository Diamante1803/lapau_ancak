<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\PengajuanLelang;
use App\Models\Perkara;
use App\Models\Barang;
use App\Models\Lelang;

use App\Policies\PengajuanLelangPolicy;
use App\Policies\PerkaraPolicy;
use App\Policies\BarangPolicy;
use App\Policies\LelangPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PengajuanLelang::class => PengajuanLelangPolicy::class,
        Perkara::class => PerkaraPolicy::class,
        Barang::class => BarangPolicy::class,
        Lelang::class => LelangPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}