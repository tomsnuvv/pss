<?php

namespace App\Providers;

use App\Nova\Metrics\FindingsByCategory;
use App\Nova\Metrics\FindingsBySeverity;
use App\Nova\Metrics\FindingsCount;
use App\Nova\Metrics\ProjectsBySeverity;
use App\Nova\Metrics\TopInstalls;
use App\Nova\Metrics\VulnerabilitiesCount;
use App\Nova\Metrics\WPPSSIssues;
use App\Nova\Metrics\WPPSSStatus;
use Endouble\ImportStats\ImportStats;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->loadCustomStyle();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return isset($user->id) && $user->id;
        });
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new FindingsBySeverity,
            new FindingsByCategory,
            new ProjectsBySeverity,

            new VulnerabilitiesCount,
            new ImportStats,
            new FindingsCount,

            new TopInstalls,
            new WPPSSStatus,
            new WPPSSIssues,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [];
    }

    /**
     * Loads custom styles
     *
     * @return void
     */
    public function loadCustomStyle()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::style('customised-styles', public_path('css/app.css'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
