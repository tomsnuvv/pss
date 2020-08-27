<?php

namespace App\Nova;

use Eminiarts\Tabs\ActionsInTabs;
use Eminiarts\Tabs\Tabs;
use Endouble\Badge\Badge;
use Endouble\Expiration\Expiration;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class Domain extends Resource
{
    use ActionsInTabs;

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
    public static $model = 'App\Models\Domain';

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
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['whois', 'host', 'parent', 'certificate', 'websites', 'dns', 'findings'];

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 100;

    /**
     * Get the fields displayed by the resource.
     *
     * @todo Parent Domain don't seem to be working.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Domain', [
                ID::make()->sortable(),
                Boolean::make('Key'),
                Boolean::make('Wildcard'),
                Text::make('name')
                    ->sortable()
                    ->rules('required', 'unique:domains,name,{{resourceId}}'),
                BelongsTo::make('Parent domain', 'parent', 'App\Nova\Domain')->searchable()->onlyOnDetail(),
                BelongsTo::make('Host')->searchable()->exceptOnForms(),
                BelongsTo::make('Certificate')->exceptOnForms(),
            ]))->withToolbar(),
            (new Tabs('Relations', [
                'Relations' => [
                    MorphMany::make('Projects'),
                    Expiration::make('Whois expiration', null, function () {
                        $whois = $this->whois ? $this->whois->daysToExpire() : null;

                        if (!$whois && isset($this->parent->whois)) {
                            $whois = $this->parent->whois ? $this->parent->whois->daysToExpire() : null;
                        }

                        return $whois;
                    })->onlyOnIndex(),
                    HasOne::make('Whois', 'whois', 'App\Nova\Whois')->onlyOnDetail(),
                    HasMany::make('DNS', 'dns', 'App\Nova\DNS'),
                    BelongsToMany::make('Websites')->searchable(),
                    Text::make('Websites', function () {
                        return $this->websites()->count();
                    })->onlyOnIndex(),
                    HasMany::make('Nameservers'),

                    HasMany::make('Subdomains'),
                    Text::make('Subdomains', function () {
                        return $this->subdomains()->count();
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
            new Filters\Domains\TopLevel,
            new Filters\Domains\Expiration,
            new Filters\Key,
            new Filters\Domains\Wildcard,
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
            new Actions\AuditDomain,
            new Actions\Key,
        ];
    }
}
