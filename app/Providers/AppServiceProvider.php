<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    /**
     * Configure application-wide framework defaults.
     *
     * This boot hook establishes the default string length and the named API
     * rate limiters used by the HTTP route definitions.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureApiRateLimiting();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        Model::shouldBeStrict(! app()->isProduction());

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
            : null,
        );
    }

    /**
     * Configure rate limits for the stateless API surface.
     *
     * Authentication and password-reset limits key by identity and IP, while
     * authenticated requests key by bearer token and client address.
     */
    private function configureApiRateLimiting(): void
    {
        RateLimiter::for('api-auth', function (Request $request): Limit {
            $email = Str::lower($request->string('email')->trim()->toString());

            return Limit::perMinute(5)->by('api-auth:'.$email.'|'.$request->ip());
        });

        RateLimiter::for('api-password-reset', function (Request $request): Limit {
            $email = Str::lower($request->string('email')->trim()->toString());

            return Limit::perMinute(3)->by('api-reset:'.$email.'|'.$request->ip());
        });

        RateLimiter::for('api', function (Request $request): Limit {
            $token = $request->bearerToken();
            $identity = is_string($token) ? hash('sha256', $token) : $request->ip();

            return Limit::perMinute(120)->by('api:'.$identity.'|'.$request->ip());
        });

        RateLimiter::for('api-tokens', fn (Request $request): Limit => Limit::perMinute(10)->by(
            'api-tokens:'.(is_int($userId = $request->user()?->getAuthIdentifier()) || is_string($userId)
                ? $userId
                : $request->ip()),
        ));
    }
}
