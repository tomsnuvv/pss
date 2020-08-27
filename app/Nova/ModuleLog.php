<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Endouble\Badge\Badge;
use Laravel\Nova\Fields\Code;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class ModuleLog extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Admin';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\ModuleLog';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 100;

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['module', 'status', 'model'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Module'),
            Badge::make('Status', 'status_id', function () {
                return $this->status ? $this->status->name : '';
            })->type('Module Log Status')->sortable(),
            MorphTo::make('Model'),
            Text::make('Results')->sortable(),
            Code::make('Details')->hideFromIndex(),
            DateTime::make('Executed At')->sortable(),
            DateTime::make('Finished At')->sortable()->hideFromIndex(),
            Text::make('Duration', 'duration', function () {
                if ($this->duration !== null) {
                    return gmdate('H:i:s', $this->duration);
                }
            })->sortable(),
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
            new Filters\ModuleLogs\ModuleType,
            new Filters\ModuleLogs\Status,
        ];
    }
}
