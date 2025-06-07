<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Set all required variables for L5-Swagger views
        View::composer(['vendor.l5-swagger.index', 'l5-swagger::index'], function ($view) {
            // Set the documentation name
            $view->with('documentation', 'default');

            // Set the documentation title from config
            $documentationTitle = config('l5-swagger.documentations.default.api.title', 'Netumo Clone API');
            $view->with('documentationTitle', $documentationTitle);

            // Set urlsToDocs with the documentation URL
            $apiUrl = url(config('l5-swagger.documentations.default.routes.api', 'api/documentation'));
            $view->with('urlsToDocs', [
                $documentationTitle => $apiUrl
            ]);

            // Set path configuration
            $view->with('useAbsolutePath', config('l5-swagger.documentations.default.paths.use_absolute_path', true));

            // Set additional configuration options that might be needed
            $view->with('operationsSorter', config('l5-swagger.defaults.operations_sort', null));
            $view->with('configUrl', config('l5-swagger.additional_config_url', null));
            $view->with('validatorUrl', config('l5-swagger.validator_url', null));
        });
    }
}
