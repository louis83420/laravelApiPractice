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
        $this->registerPolicies();


        Passport::tokensCan([
            'admin' => 'Access all user data',
            'read' => 'Read-only access to data',
            'write' => 'Write access to data',
            'profile' => 'Read user profile',
            'email' => 'Read user email',
        ]);
        Passport::enablePasswordGrant();


        // 設定 Personal Access Tokens 的有效期限為 6 個月
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // 一般存取15天 刷新存取30天
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));

    }
}
