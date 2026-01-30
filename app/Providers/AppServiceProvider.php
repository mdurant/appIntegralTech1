<?php

namespace App\Providers;

use App\Listeners\RegisterUserSession;
use App\Models\PaymentSimulation;
use App\Models\Rating;
use App\Policies\PaymentSimulationPolicy;
use App\Policies\RatingPolicy;
use App\Support\TenantContext;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->registerEventListeners();
        $this->configureApiRateLimiting();
    }

    protected function configureApiRateLimiting(): void
    {
        RateLimiter::for('api:v1', function (Request $request) {
            $user = $request->user();
            $limit = $user?->apiRateLimit() ?? 0;

            return Limit::perMinute(max(1, $limit))
                ->by($user?->id ?? $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => __('Demasiadas peticiones. Por favor, inténtalo de nuevo en un minuto.'),
                    ], 429);
                });
        });
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

    protected function registerEventListeners(): void
    {
        Event::listen(Login::class, RegisterUserSession::class);
    }
}
