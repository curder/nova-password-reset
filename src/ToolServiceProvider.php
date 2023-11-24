<?php

namespace Mastani\NovaPasswordReset;

use Laravel\Nova\Nova;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

use function config;

class ToolServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('password-reset')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasRoutes('api', 'inertia');

        Nova::serving(function () {
            $locale = app()->getLocale();
            $localeFile = lang_path('vendor/password-reset').'/'.$locale.'.json';
            $filePath = __DIR__.'/../resources/lang/'.$locale.'.json';

            File::exists($localeFile)
                ? Nova::translations($localeFile)
                : Nova::translations($filePath);

            Nova::provideToScript(['minPasswordSize' => config('password-reset.min_password_size', 6)]);
        });
    }

    public function packageRegistered(): void
    {
        // Register router for view
        Nova::router(config('password-reset.middleware'), 'password-reset')
            ->group(__DIR__.'/../routes/inertia.php');

        // Register router for api
        Route::middleware(config('password-reset.api_middleware'))
            ->prefix('vendor/password-reset')
            ->group(__DIR__.'/../routes/api.php');
    }
}
