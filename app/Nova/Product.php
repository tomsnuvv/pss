<?php

namespace App\Nova;

use Eminiarts\Tabs\Tabs;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;

class Product extends Resource
{
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
    public static $model = 'App\Models\Product';

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
        'code', 'name',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['vendor', 'type', 'license', 'vulnerabilities', 'installations'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Product', [
                ID::make()->sortable(),
                BelongsTo::make('Type', 'type', 'App\Nova\ProductType')->searchable()->nullable(),
                BelongsTo::make('License', 'license',
                    'App\Nova\ProductLicense')->hideFromIndex()->searchable()->nullable(),
                BelongsTo::make('Vendor')->searchable(),
                Text::make('Code')->sortable()->rules('required'),
                Text::make('Name')->hideFromIndex(),
                Textarea::make('Description'),
                Text::make('Website')->hideFromIndex()->rules('nullable', 'url'),
                Text::make('Latest Version')->hideFromIndex(),
                DateTime::make('Latest Info Check')->onlyOnDetail(),
            ]))->withToolbar(),
            new Tabs('Relations', [
                'Relations' => [
                    BelongsToMany::make('Vulnerabilities'),
                    Text::make('Vulnerabilities', function () {
                        return $this->vulnerabilities()->count();
                    })->onlyOnIndex(),
                    HasMany::make('Installations'),
                    Text::make('Installations', function () {
                        return $this->installations()->count();
                    })->onlyOnIndex(),
                    HasMany::make('Synonyms', 'synonyms', 'App\Nova\ProductSynonym'),
                ],
            ]),
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
            new Filters\Products\Type,
            new Filters\Products\WithInstallations,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [
            new Lenses\Products\MostInstallations,
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
            new Actions\Products\Merge,
        ];
    }
}
