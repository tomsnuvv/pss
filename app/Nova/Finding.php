<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Date;
use Endouble\Badge\Badge;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;

class Finding extends Resource
{
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
    public static $model = 'App\Models\Finding';

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
        'title', 'details',
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
    public static $with = ['status', 'target', 'severity'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Tabs('Finding', [
                'Finding' => [
                    ID::make()->sortable(),
                    Text::make('Title')->sortable(),
                    BelongsTo::make('Finding Status', 'status')->onlyOnForms(),
                    Badge::make('Status', 'status', function () {
                        return $this->status->name;
                    })->type('Finding Status')->exceptOnForms(),
                    BelongsTo::make('Severity')->onlyOnForms(),
                    Badge::make('Severity', null, function () {
                        return isset($this->severity->name) ? $this->severity->name : 'Unknown';
                    })->default('Unknown')->exceptOnForms(),
                    MorphTo::make('Target')->types([
                        Domain::class,
                        Host::class,
                        Website::class,
                        Installation::class,
                        Repository::class,
                    ]),
                    MorphTo::make('ChildTarget')->types([
                        Port::class,
                        Certificate::class,
                    ])->nullable(),
                    BelongsTo::make('Installation')->searchable()->nullable(),
                    BelongsTo::make('Vulnerability')->hideFromIndex()->searchable()->nullable(),
                    BelongsTo::make('Vulnerability Type', 'type')->hideFromIndex()->searchable(),
                    Markdown::make('Details')->hideFromIndex()->alwaysShow(),
                    BelongsTo::make('Module')->onlyOnDetail(),
                    DateTime::make('Created at')->exceptOnForms(),
                    DateTime::make('Last Module check', function () {
                        if (!$this->module) {
                            return null;
                        }
                        $log = $this->getModuleLog();
                        return $log ? $log->finished_at : null;
                    }),
                ],
                'Vulnerability' => [
                    Textarea::make('Description', 'vulnerability.description')->onlyOnDetail()->alwaysShow(),
                    Markdown::make('Proof of Concept', 'vulnerability.proof_of_concept')->onlyOnDetail()->alwaysShow(),
                    Code::make('Vulnerable Code', 'vulnerability.vulnerable_code')->onlyOnDetail(),
                    Date::make('Date', 'vulnerability.date')->onlyOnDetail(),
                ],
                'Type' => [
                    Textarea::make('Description', 'type.description')->onlyOnDetail()->alwaysShow(),
                    Textarea::make('Details', 'type.attack_details')->onlyOnDetail()->alwaysShow(),
                    Markdown::make('Remediation', 'type.remediation')->onlyOnDetail()->alwaysShow(),
                ],
                'Projects' => [
                    MorphMany::make('Projects'),
                ]
            ]))->withToolbar(),
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
            new Filters\VulnerabilityType,
            new Filters\Severity,
            new Filters\Findings\Status,
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
            new Actions\Findings\ChangeStatus,
            new Actions\Findings\IgnoreAll,
            new Actions\Findings\ReCheck,
        ];
    }
}
