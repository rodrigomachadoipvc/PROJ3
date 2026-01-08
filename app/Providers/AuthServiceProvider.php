<?php

namespace App\Providers;

use App\Models\Anexo;
use App\Models\Grupo;
use App\Policies\AnexoPolicy;
use App\Policies\GrupoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Anexo::class => AnexoPolicy::class,
        Grupo::class => GrupoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}