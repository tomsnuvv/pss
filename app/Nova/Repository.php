<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Endouble\Badge\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\ActionsInTabs;
use Laravel\Nova\Actions\ActionResource;

class Repository extends Resource
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
    public static $model = 'App\Models\Repository';

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
        'name', 'url', 'clone_url',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['installations', 'findings'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Repository', [
                ID::make()->sortable(),
                Text::make('Name')->sortable(),
                Text::make('URL')->sortable()->hideFromIndex(),
                Text::make('Clone URL')->sortable()->hideFromIndex(),
                Boolean::make('Public'),
            ]))->withToolbar(),
            (new Tabs('Relations', [
                'Relations' => [
                    MorphToMany::make('Projects'),
                    MorphMany::make('Installations'),
                    Text::make('Installations', function () {
                        return $this->installations()->count();
                    })->onlyOnIndex(),
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
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            new Actions\AuditRepository,
        ];
    }
}
