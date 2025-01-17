<?php

namespace App\Providers;
use Laravel\Passport\Passport;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();


        Passport::tokensCan([
            'admin' => 'Access all user data',
            'read' => 'Read-only access to data',
            'write' => 'Write access to data',
        ]);
    }
}
