<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Heading;
use Endouble\Expiration\Expiration;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Eminiarts\Tabs\Tabs;

class Certificate extends Resource
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
    public static $model = 'App\Models\Certificate';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'subject_common_name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
        'subject_common_name', 'subject_org_unit',
        'issuer_common_name', 'issuer_org', 'issuer_country', 'issuer_county', 'issuer_locality',
        'creation_date', 'expiration_date',
        'serial', 'signature_algorithm',
    ];

    /**
     * The relationships that should be eager loaded on index queries.
     *
     * @var array
     */
    public static $with = ['findings'];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            (new Panel('Certificate', [
                ID::make()->sortable(),
                Heading::make('General'),
                Text::make('Subject Common Name'),
                Text::make('Name')->hideFromIndex(),
                DateTime::make('Expiration Date')->hideFromIndex(),
                Expiration::make('Expires in', 'expiration_date', function () {
                    return $this->daysToExpire();
                })->sortable()->exceptOnForms(),
                DateTime::make('Creation Date')->sortable(),
                Text::make('Serial')->hideFromIndex(),
                Text::make('Key Type')->hideFromIndex(),
                Text::make('Key Length')->hideFromIndex(),
                Text::make('Signature Algorithm')->hideFromIndex(),

                Heading::make('Subject'),
                Text::make('Subject Org Unit')->hideFromIndex(),

                Heading::make('Issuer'),
                Text::make('Issuer Common Name')->hideFromIndex(),
                Text::make('Issuer Org')->hideFromIndex(),
                Text::make('Issuer Country')->hideFromIndex(),
                Text::make('Issuer County')->hideFromIndex(),
                Text::make('Issuer Locality')->hideFromIndex(),
            ]))->withToolbar(),
            new Tabs('Relations', [
                'Relations' => [
                    MorphToMany::make('Projects'),
                    HasMany::make('Domains'),
                    BelongsToMany::make('Hosts'),
                    MorphMany::make('Module Logs', 'moduleLogs'),
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
            new Filters\Certificates\Expiration
        ];
    }
}
