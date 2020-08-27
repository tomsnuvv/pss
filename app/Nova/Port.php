<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\MorphOne;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Endouble\Badge\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;

class Port extends Resource
{
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
    public static $model = 'App\Models\Port';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'port';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'protocol', 'port', 'service',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['installation'];

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
    public function fields(Request $request)
    {
        return [
            (new Panel('Port', [
                ID::make(),
                BelongsTo::make('Host')->sortable()->searchable(),
                Text::make('Protocol')->sortable(),
                Text::make('Port')->sortable(),
                Text::make('Service')->sortable(),
                Text::make('Product', function () {
                    if ($this->installation) {
                        return $this->installation->product->name;
                    }
                })->onlyOnIndex(),
                Text::make('Version', function () {
                    if ($this->installation) {
                        return $this->installation->version;
                    }
                })->onlyOnIndex(),
                Text::make('Findings', function () {
                    return $this->findings()->open()->count();
                })->onlyOnIndex(),
                Badge::make('Severity', function () {
                    if ($this->findings) {
                        return $this->getMaxSeverity() ?: 'Unknown';
                    }
                })->onlyOnIndex(),
            ]))->withToolbar(),
            new Tabs('Relations', [
                'Relations' => [
                    MorphOne::make('Installation'),
                    MorphMany::make('Findings'),
                    MorphToMany::make('Projects'),
                ]
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
            new Filters\FindingsWithSeverity,
            new Filters\WithFindings,
            new Filters\Ports\KeyHosts,
        ];
    }
}
