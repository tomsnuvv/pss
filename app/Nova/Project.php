<?php

namespace App\Nova;

use Eminiarts\Tabs\ActionsInTabs;
use Eminiarts\Tabs\Tabs;
use Endouble\Badge\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class Project extends Resource
{
    use ActionsInTabs;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'General';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Project';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Project', [
                ID::make()->sortable(),
                Text::make('Name')
                    ->rules('required', 'unique:projects,name,{{resourceId}}')
                    ->sortable(),
            ]))->withToolbar(),
            (new Tabs('Relations', [
                'Relations' => [
                    MorphToMany::make('Websites')->searchable(),
                    Text::make('Websites', function () {
                        return $this->websites()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Repositories')->searchable(),
                    Text::make('Repositories', function () {
                        return $this->repositories()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Domains')->searchable(),
                    Text::make('Domains', function () {
                        return $this->domains()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Hosts')->searchable(),
                    Text::make('Hosts', function () {
                        return $this->hosts()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Ports'),
                    Text::make('Ports', function () {
                        return $this->ports()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Certificates'),
                    Text::make('Certificates', function () {
                        return $this->certificates()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Installations'),
                    Text::make('Installations', function () {
                        return $this->installations()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Findings'),
                    Text::make('Findings', function () {
                        return $this->findings()->open()->count();
                    })->onlyOnIndex(),
                    MorphToMany::make('Organisations')->searchable(),
                    Badge::make('Severity', function () {
                        return $this->getMaxSeverity();
                    })->onlyOnIndex(),
                    MorphMany::make(__('Actions'), 'actions', ActionResource::class),
                ],
            ]))->defaultSearch(true),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\FindingsWithSeverity,
            new Filters\WithFindings,
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new Actions\Projects\AuditProject,
            new Actions\Projects\GenerateReport,
            new Actions\Projects\Delete,
        ];
    }
}
