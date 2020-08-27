<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;

class Organisation extends Resource
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
    public static $model = 'App\Models\Organisation';

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
            (new Panel('Organisation', [
                ID::make()->sortable(),
                Text::make('name')
                    ->sortable()
                    ->rules('required', 'unique:organisations,name,{{resourceId}}'),
            ]))->withToolbar(),
            new Tabs('Relations', [
                'Relations' => [
                    MorphToMany::make('Projects'),
                ]
            ]),
        ];
    }
}
