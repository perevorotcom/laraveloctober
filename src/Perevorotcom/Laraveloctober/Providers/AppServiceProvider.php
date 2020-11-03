<?php

namespace Perevorotcom\Laraveloctober\Providers;

use Illuminate\Support\ServiceProvider;
use Perevorotcom\Laraveloctober\Classes\SystemTranslate;
use Perevorotcom\Laraveloctober\Extensions\BladeExtensions;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        BladeExtensions::extend();

        $this->setMiddlewareAliases($router);

        $router->pushMiddlewareToGroup('backend', \Perevorotcom\Laraveloctober\Http\Middleware\CheckBackendHeaders::class);

        $this->publishes([
            __DIR__.'/../../resources/views' => resource_path('views'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../Longread' => app_path('Longread'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../Models/Sample.php' => app_path('Models/Sample.php'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../Models/Settings.php' => app_path('Models/Settings.php'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../../.env.example' => base_path('.env.example.laraveloctober'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../../config' => config_path(),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../../routes/laraveloctober.php' => base_path('routes/laraveloctober.php'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../../routes/console.php' => base_path('routes/console.php'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../Http/Controllers/PageController.php' => app_path('Http/Controllers/PageController.php'),
        ], 'laraveloctober');

        $this->publishes([
            __DIR__.'/../Http/Controllers/ControllerSample.php' => app_path('Http/Controllers/ControllerSample.php'),
        ], 'laraveloctober');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('translate', function () {
            return new SystemTranslate();
        });
    }

    /**
     * Set application middleware aliases.
     *
     * @return void
     */
    private function setMiddlewareAliases(\Illuminate\Routing\Router $router)
    {
        $middlewares = [
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'redirectTrailingSlash' => \Perevorotcom\Laraveloctober\Http\Middleware\RedirectTrailingSlash::class,
            'caching' => \Perevorotcom\Laraveloctober\Http\Middleware\CachingMiddleware::class,
        ];

        foreach ($middlewares as $middleware => $middlewareClass) {
            $router->aliasMiddleware($middleware, $middlewareClass);
        }
    }
}
