<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();
        $this->customSchema();
    }

    protected function configureDefaults(): void
    {
        if (config('app.env') == 'production') {
            URL::forceScheme('https');
        }

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

    protected function customSchema(): void
    {
        /* Custom Blueprint Schema */
        Blueprint::macro('manageBy', function () {
            $this->string('created_by')->nullable();
            $this->string('updated_by')->nullable();
        });

        Builder::macro('createWithManageBy', function ($table, Closure $callback) {
            return $this->create($table, function (Blueprint $table) use ($callback) {
                $callback($table);

                $table->manageBy();
            });
        });
    }
}
