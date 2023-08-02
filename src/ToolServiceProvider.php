<?php

namespace Mastani\NovaPasswordReset;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Laravel\Nova\Http\Middleware\Authenticate;
use Mastani\NovaPasswordReset\Http\Middleware\Authorize;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ToolServiceProvider extends PackageServiceProvider
{

    public function configurePackage(Package $package): void
    {
        $package
            ->name('password-reset')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasRoutes('api', 'inertia');

        Nova::router(['nova', Authenticate::class, Authorize::class], 'password-reset')
            ->group(__DIR__ . '/../routes/inertia.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('vendor/password-reset')
            ->group(__DIR__ . '/../routes/api.php');

        Nova::serving(function () {
            $locale = app()->getLocale();
            $localeFile = lang_path('vendor/password-reset') . '/' . $locale . '.json';
            $filePath = __DIR__ . '/../resources/lang/' . $locale . '.json';

            File::exists($localeFile)
                ? Nova::translations($localeFile)
                : Nova::translations($filePath);
        });
    }
}
