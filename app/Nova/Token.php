<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Textarea;
use Illuminate\Http\Request;

class Token extends Resource
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
    public static $model = 'App\Models\Token';

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
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(),
            MorphTo::make('Model')->types([
                Website::class,
                Host::class,
            ])->searchable(),
            Text::make('Token', 'token')->displayUsing(function ($token) {
                return substr($token, 0, 4) . ' ... ';
            })->onlyOnIndex(),
            Textarea::make('Token', 'token')->onlyOnDetail(),
            Text::make('Token')->onlyOnForms(),
        ];
    }
}
