<?php

namespace App\Providers;

use App\Models\PaymentSimulation;
use App\Models\Rating;
use App\Policies\PaymentSimulationPolicy;
use App\Policies\RatingPolicy;
use App\Support\TenantContext;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->configureDefaults();
        $this->configureAuthorization();
    }

    /**
     * Register the application's policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Rating::class, RatingPolicy::class);
        Gate::policy(PaymentSimulation::class, PaymentSimulationPolicy::class);
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }

    protected function configureAuthorization(): void
    {
        Gate::before(function ($user) {
            if (method_exists($user, 'isAdministrator') && $user->isAdministrator()) {
                return true;
            }

            return null;
        });
    }
}
