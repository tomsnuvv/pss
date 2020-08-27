<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Endouble\Badge\Badge;
use Illuminate\Http\Request as HttpRequest;

class Request extends Resource
{
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
    public static $model = 'App\Models\Request';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'path';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'path'
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['content'];

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 100;

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(HttpRequest $request)
    {
        return [
            ID::make(),
            BelongsTo::make('Website')->sortable(),
            Text::make('Method')->sortable(),
            Text::make('Path')->sortable(),
            Badge::make('Status'),
            BelongsTo::make('Content', 'content', 'App\Nova\RequestContent'),
        ];
    }
}
