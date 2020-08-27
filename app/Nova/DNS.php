<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphTo;
use Illuminate\Http\Request;

class DNS extends Resource
{
    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'DevOps';

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\DNS';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'value';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'type', 'class', 'ttl', 'pri',
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
            ID::make(),
            Text::make('Type')->sortable(),
            Text::make('Value')->sortable(),
            MorphTo::make('Target')->types([
                Domain::class,
                Host::class,
                Website::class,
            ])->sortable()->searchable(),
            Text::make('Class')->sortable(),
            Text::make('TTL')->sortable(),
            Text::make('PRI')->sortable(),
        ];
    }
}
