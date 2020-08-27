<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Endouble\Badge\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\ActionsInTabs;
use Laravel\Nova\Actions\ActionResource;

class Host extends Resource
{
    use ActionsInTabs;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'DevOps';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Host';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'ip';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'ip',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['type', 'websites', 'ports', 'findings'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Host', [
                ID::make()->sortable(),
                Boolean::make('Key'),
                BelongsTo::make('Host Type', 'type'),
                Text::make('Name')
                    ->sortable()
                    ->rules('nullable', 'unique:hosts,name,{{resourceId}}'),
                Text::make('IP')
                    ->rules('required', 'unique:hosts,ip,{{resourceId}}'),
            ]))->withToolbar(),
            (new Tabs('Relations', [
                'Relations' => [
                    MorphToMany::make('Projects'),
                    HasMany::make('Ports'),
                    Text::make('Ports', function () {
                        return $this->ports()->count();
                    })->onlyOnIndex(),
                    BelongsToMany::make('Certificates'),
                    BelongsToMany::make('Websites'),
                    Text::make('Websites', function () {
                        return $this->websites()->count();
                    })->onlyOnIndex(),
                    MorphMany::make('DNS', 'targetDNS', 'App\Nova\DNS'),
                    HasMany::make('Domain', 'domains'),
                    MorphMany::make('Findings'),
                    Text::make('Findings', function () {
                        return $this->findings()->open()->count();
                    })->onlyOnIndex(),
                    Badge::make('Severity', function () {
                        return $this->getMaxSeverity();
                    })->onlyOnIndex(),
                    MorphMany::make('Module Logs', 'moduleLogs'),
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
            new Filters\Key,
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
            new Actions\AuditHost,
            new Actions\Key,
        ];
    }
}
