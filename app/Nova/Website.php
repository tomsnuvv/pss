<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Image;
use Endouble\Badge\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;
use Eminiarts\Tabs\ActionsInTabs;
use Laravel\Nova\Actions\ActionResource;
use Illuminate\Support\Facades\Storage;

class Website extends Resource
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
    public static $model = 'App\Models\Website';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'url';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'url',
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
    public static $with = ['environment'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Website', [
                ID::make()->sortable(),
                Boolean::make('Key'),
                Boolean::make('Crawl'),
                BelongsTo::make('Environment'),
                Text::make('URL')
                    ->sortable()
                    ->rules('required', 'url', 'unique:websites,url,{{resourceId}}'),
                Badge::make('Status')->exceptOnForms(),
                Image::make('Snapshot')->onlyOnDetail()->preview(function () {
                    return Storage::disk('public')->url('websites/snapshots/' . $this->id . '.png');
                }),
            ]))->withToolbar(),
            (new Tabs('Relations', [
                'Relations' => [
                    BelongsToMany::make('Domains')->onlyOnDetail()->searchable(),
                    BelongsToMany::make('Hosts'),
                    HasMany::make('Requests'),
                    HasMany::make('Headers'),
                    HasOne::make('Token')->hideFromIndex(),
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
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new Filters\Environment,
            new Filters\Key,
            new Filters\Websites\Crawl,
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
            new Actions\AuditWebsite,
            new Actions\Key,
            new Actions\Websites\Crawl,
        ];
    }
}
