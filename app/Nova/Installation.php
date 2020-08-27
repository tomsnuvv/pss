<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Titasgailius\SearchRelations\SearchesRelations;
use Maatwebsite\LaravelNovaExcel\Actions\DownloadExcel;
use Endouble\Badge\Badge;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;

class Installation extends Resource
{
    use SearchesRelations;

    /**
     * Determine if relations should be searched globally.
     *
     * @var array
     */
    public static $searchRelationsGlobally = false;

    /**
     * The logical group associated with the resource.
     *
     * @var string
     */
    public static $group = 'Security';

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\Models\Installation';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'version', 'title',
    ];

    /**
     * The relationship columns that should be searched.
     *
     * @var array
     */
    public static $searchRelations = [
        'product' => ['name', 'code'],
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['source', 'product.type', 'findings'];

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
            (new Panel('Installation', [
                ID::make()->sortable(),
                Text::make('Type', function () {
                    if (isset($this->product->type)) {
                        return $this->product->type->name;
                    }
                })->onlyOnIndex(),
                MorphTo::make('Source')->types([
                    Host::class,
                    Website::class,
                    Repository::class,
                ])->searchable(),
                MorphTo::make('ChildSource')->types([
                    Port::class,
                ])->searchable()->onlyOnDetail(),
                BelongsTo::make('Product'),
                Text::make('Version')->sortable(),
                Text::make('Title')->onlyOnDetail(),
                DateTime::make('Created At')->onlyOnDetail(),
                DateTime::make('Updated At')->onlyOnDetail(),
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
                    HasMany::make('Findings'),
                    MorphToMany::make('Projects'),
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
            new Filters\Installations\SourceType,
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
            new DownloadExcel,
        ];
    }
}
